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

composer:
	docker-compose exec parking composer install -o