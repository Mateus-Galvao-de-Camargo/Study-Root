# Atalhos para os comandos mais comuns. Tudo opcional — você pode rodar
# os comandos correspondentes manualmente.

.PHONY: help install up down logs test shell migrate clean smoke cleanup-legacy

help: ## Lista os alvos disponíveis
	@grep -E '^[a-zA-Z_-]+:.*## ' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS=":.*## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

install: ## composer install dentro de um container temporário
	docker run --rm -v "$$PWD/src:/app" -w /app composer:2 install --no-interaction

up: ## sobe app + Postgres (Postgres local)
	docker compose up --build

down: ## derruba e remove os volumes (cuidado: apaga dados locais)
	docker compose down -v

logs: ## logs do app
	docker compose logs -f app

shell: ## abre bash dentro do container app
	docker compose exec app bash

migrate: ## roda back-end/migrate.php no container já em execução
	docker compose exec app php /var/www/html/back-end/migrate.php

test: ## roda PHPUnit dentro do container app (precisa de `make up` antes)
	docker compose exec -w /var/www/html app sh -c \
		"command -v composer >/dev/null || (curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --quiet); \
		 composer install --no-interaction --quiet && \
		 vendor/bin/phpunit --testdox"

clean: ## remove vendor e cache local
	rm -rf src/vendor src/.phpunit.cache

smoke: ## roda smoke test end-to-end (precisa de `make up` antes)
	bash scripts/smoke-test.sh

cleanup-legacy: ## git rm dos arquivos deprecated (Dockerfile.prod, init-*.sh, nginx.conf, redis.conf, phinx.php, SQLs antigos)
	bash scripts/cleanup-deprecated.sh
