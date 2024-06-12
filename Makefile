.PHONY: up down build start stop restart logs ps

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
