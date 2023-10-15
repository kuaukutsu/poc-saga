# Proof of Concept: SAGA

- https://microservices.io/patterns/data/saga.html
- https://learn.microsoft.com/ru-ru/azure/architecture/reference-architectures/saga/saga


## Проблема

Если совсем коротко, то это согласованность действий, и принцип "либо всё, либо ничего".

### Различные модули

Допустим, что у нас есть блок данных, которые необходимо поместить в N+1 модуль.
Организовать транзакцию средствами базы данных мы не можем, потому что работаем в нескольких базах.

## Решение

- Любое сложное действие можно разбить на несколько простых.
- Чем меньше будет размер "действия", тем больше будет над ним контроля.
- Для любого атомарного действия, можно написать его анти-действие.

Если совсем кратко, то решение заключается в том, чтобы разделить один набор действий на максимально атомарные шаги,
и для каждого шага выполнения написать его компенсирующий шаг.

Из этого получаем следующую схему:

- Любое действие можно представить как **Транзакцию (transaction)**, которая состоит из отдельных **Шагов (step)**.
- Каждый **шаг** умеет выполнять два действия: **commit** и **rollback**, и если выполнить сначала commit, а затем
  rollback, то состояние системы должно быть ровно таким же как если бы ничего не выполнялось.
- Все шаги выполняются последовательно, если какой-то шаг не выполнился, то вся транзакция не выполнилась.
- Соответственно у транзакции есть только два состояния: либо **все** шаги **выполнились**, либо **ни один** шаг **не
  выполнился**.

## Реализация

Описываем транзакцию как набор шагов

```php
final class TestTransaction extends TransactionBase
{
    public function steps(): TransactionStepCollection
    {
        return new TransactionStepCollection(
            ObjectBaseDto::hydrate(
                [
                    'class' => TestStepOne::class,
                ]
            ),
            ObjectBaseDto::hydrate(
                [
                    'class' => TestStepTwo::class,
                ]
            ),
            ObjectBaseDto::hydrate(
                [
                    'class' => TestStepAnother::class,
                ]
            ),
        );
    }
}
```

Описываем шаги

```php
final class TestStep extends TransactionStepBase
{
    public function commit(): bool
    {
        // Полезная работа: запись в хранилище, в очередь...
        
        $this->save(
            TestStepDto::hydrate(
                [
                    'id' => ..., // например ID полученный на запись в хранилище                   
                ]
            )
        );
    
        return true;
    }

    public function rollback(): bool
    {
        /** @var TestStepDto $data */
        $data = $this->get(self::class); // получаем Состояние сохранённое при commit
        
        // Полезная работа: удаление из хранилища, компенсационная задача в очередь 
    
        return true;
    }
}
```

Инициируем экземпляр транзакции, и запускаем

```php
$transaction = new TestTransaction();

/** @var TransactionRunner $transactionRunner */
$transactionRunner->run($transaction);
```

Так же есть возможность подписаться на события commit и rollback, для того чтобы получить доступ к результату из вне.

```php
$transaction = new TestTransaction();

/** @var TransactionRunner $transactionRunner */
$transactionRunner->run(
    $transaction,
    new CommitCallback(
        static function (TransactionStateCollection $data) {
            ...
        }
    ),
    new RollbackCallback(
        static function (TransactionStateCollection $data, Exception $exception) {
            ...
        }
    ),
);
```

## Получаем данные из транзакции

```php

/** @var TransactionRunner $transactionRunner */
$dto = $transactionRunner->run(
    new TestTransaction()
);

/** @var TransactionDto $dto */

/** @var TestStepDto $dataFromTestStep */
$dataFromTestStep = $dto->state->getData(TestStep::class);
```


## Docker

```shell
docker pull ghcr.io/kuaukutsu/php:8.1-cli
```

Container:
- `ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli` (**default**)
- `jakzal/phpqa:php${PHP_VERSION}`

shell

```shell
docker run --init -it --rm -v "$(pwd):/app" -w /app ghcr.io/kuaukutsu/php:8.1-cli sh
```

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
make phpunit
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
make psalm
```

### Code Sniffer

```shell
make phpcs
```

### Rector

```shell
make rector
```
