.PHONY: help install up stop exec ssh-symlink composer-install composer-update

.DEFAULT_GOAL := help
CONTAINER := php
COMMAND := /bin/bash

help: ## Affiche cette aide.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Démarre les conteneurs.
	@docker compose up -d

stop: ## Arrête les conteneurs.
	@docker compose stop

install: ## Configure le projet et installe les dépendances.
	@echo "Configuration du projet..."
	@cp -n .env.example .env || true
	@mkdir -p documents
	@cp -n htdocs/conf/conf.php.example htdocs/conf/conf.php || true

	@echo "\nDémarrage de l'application..."
	@docker compose up -d

	@echo "\n------------------------------------------------"
	@echo "Installation terminée."
	@echo "------------------------------------------------\n"

exec: ## Exécute une commande dans le conteneur PHP (par défaut : /bin/bash).
	docker compose exec $(CONTAINER) $(COMMAND)
