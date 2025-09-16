-include .env

.PHONY: help \
	up-leaseboard down-leaseboard exec-php-l \
	up-monk down-monk exec-php-m \
	stop

.DEFAULT_GOAL := help
CONTAINER := php
COMMAND := /bin/bash

help: ## Affiche cette aide
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

## -------------------- Leaseboard --------------------
up-leaseboard: ## Démarre Leaseboard
	docker compose -f docker-compose.leaseboard.yml up -d

down-leaseboard: ## Stoppe Leaseboard
	docker compose -f docker-compose.leaseboard.yml down

exec-php-l: ## Ouvre un shell dans le conteneur PHP de Leaseboard
	docker compose -f docker-compose.leaseboard.yml exec $(CONTAINER) $(COMMAND)

## -------------------- Monk --------------------
up-monk: ## Démarre Monk
	docker compose -f docker-compose.monk.yml up -d

down-monk: ## Stoppe Monk
	docker compose -f docker-compose.monk.yml down

exec-php-m: ## Ouvre un shell dans le conteneur PHP de Monk
	docker compose -f docker-compose.monk.yml exec $(CONTAINER) $(COMMAND)

## -------------------- Global --------------------
stop: ## Stoppe tous les conteneurs
	docker compose stop
