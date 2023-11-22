# Proof of Concept: SAGA

- https://microservices.io/patterns/data/saga.html
- https://learn.microsoft.com/ru-ru/azure/architecture/reference-architectures/saga/saga
- https://www.youtube.com/live/tLw8lJ-Eijk?si=6YIDa3lpvBYuGtFW


## Проблема

Если совсем коротко, то это согласованность действий и принцип "либо всё, либо ничего".

_Дисклеймер: в данном примере умышленно утрируется понятие саги, 
по условиям задачи мы работаем в модульной системе, где каждый модуль это отдельный контекст,
но сама система работает в рамках одного кластера._ 

### Множество модулей, разные контексты

Допустим, что у нас есть блок данных, который необходимо согласованно записать в N+1 модуль.
Каждый модуль представляет свой ограниченный контекст, и работает в рамках отдельной базы данных 
(или просто в рамках различных, не связанных таблиц). 

## Решение

- Любое сложное действие можно разбить на несколько простых.
- Чем меньше будет размер "действия", тем больше будет над ним контроля (атомарность).
- Для любого атомарного действия, можно написать его анти-действие.

Решение заключается в том, чтобы разделить один набор действий на максимально простые, атомарные задачи,
и для каждого шага выполнения (commit) написать его компенсирующее действие (rollback).

Получаем следующую схему:

- Любое действие можно представить как **Транзакцию (transaction)**, которая состоит из **Шагов (step)**.
- Каждый **шаг** умеет выполнять два действия: **commit** и **rollback**, и если выполнить сначала commit, а затем
  rollback, то состояние системы должно быть ровно таким же как если бы ничего не выполнялось (естественно с оговорками, например auto-increment).
- Все шаги выполняются последовательно (линейность в данном контексте сильный плюс), если какой-то шаг не выполнился, то вся транзакция не выполнилась.
- Соответственно у транзакции есть только два состояния: либо **все** шаги **выполнились**, либо **ни один** шаг **не
  выполнился**.

## Реализация

_Дисклеймер: в данной реализации нет сохранения состояния транзакции, 
допускаем что мы работаем в рамках одного процесса._

Описываем транзакцию как набор шагов

```php
final class TestTransaction implements TransactionInterface
{
    public function steps(): StepCollection
    {
        return new StepCollection(
            new Step(
                OneStep::class,
                [
                    'name' => 'one',
                ]
            ),
            new Step(
                TwoStep::class,
                [
                    'name' => 'two',
                ]
            ),
            new Step(
                SaveStep::class,
            ),
        );
    }
}
```

Описываем шаги

```php
final class OneStep extends TransactionStepBase
{
    public function __construct(
        public readonly string $name,
        private readonly string $dateFormat = 'c',
    ) {
    }

    public function commit(): bool
    {
        // Полезная работа: запись в хранилище, в очередь...
    
        $this->save(
            new TestTransactionData(
                name: $this->name,
                datetime: $gmdate($this->dateFormat)
            )
        );

        return true;
    }

    public function rollback(): bool
    {
        /** @var TestTransactionData $data */
        $data = $this->current(); // получаем Состояние сохранённое при commit
        
        // Полезная работа: удаление из хранилища, компенсационная задача в очередь 
    
        return true;
    }
}
```

Инициируем экземпляр транзакции, и запускаем

```php
/** 
 * @var TransactionRunner $transactionRunner 
 * @var TransactionResult $transaction
 */
$transaction = $transactionRunner->run(
    new TestTransaction()
);
```

## Получаем данные из транзакции

```php

/** 
 * @var TransactionRunner $transactionRunner 
 * @var TransactionResult $transaction
 */
$transaction = $transactionRunner->run(
    new TestTransaction()
);

/** 
 * @var TestTransactionData $testData данные, которые были записаны как модель TestTransactionData, в конечном шаге.
 */
$testData = $transaction->state->get(TestTransactionData::class);
```

## Пример (песочница)

https://github.com/kuaukutsu/yii2-component-demo


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
