PHP_VERSION ?= 8.1

composer:
	docker run --init -it --rm -v "$$(pwd):/app" -w /app composer:latest \
		cli composer install

composer-up:
	docker run --init -it --rm -v "$$(pwd):/app" -w /app composer:latest \
		composer update

composer-dump:
	docker run --init -it --rm -v "$$(pwd):/app" -w /app composer:latest \
		composer dump-autoload

psalm:
	docker run --init -it --rm -v "$$(pwd):/app" -e XDG_CACHE_HOME=/tmp -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		./vendor/bin/psalm

phpunit:
	docker run --init -it --rm -v "$$(pwd):/app" -u $$(id -u) -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		./vendor/bin/phpunit

phpcs:
	docker run --init -it --rm -v "$$(pwd):/app" -u $$(id -u) -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		./vendor/bin/phpcs

phpcbf:
	docker run --init -it --rm -v "$$(pwd):/app" -u $$(id -u) -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		./vendor/bin/phpcbf

rector:
	docker run --init -it --rm -v "$$(pwd):/app" -u $$(id -u) -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		./vendor/bin/rector
