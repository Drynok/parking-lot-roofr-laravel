.PHONY: up down build start stop restart logs ps migrate composer

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

start:
	docker compose start

stop:
	docker compose stop

restart:
	docker compose restart

logs:
	docker compose logs -f

ps:
	docker compose ps

test:
	docker compose exec parking php artisan test

migrate:
	docker-compose exec parking php artisan migrate

migrate-down:
	docker-compose exec parking php artisan migrate:rollback

composer:
	docker-compose exec parking composer install -o

seed:
	docker-compose exec parking php artisan db:seed

swagger:
	docker-compose exec parking php artisan l5-swagger:generate
	# docker-compose exec parking ./vendor/bin/openapi --format json --output ./public/swagger.json ./app

cache-clear:
 	docker-compose exec parking php artisan optimize:clear