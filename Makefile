.PHONY: help install up stop exec ssh-symlink composer-install composer-update

.DEFAULT_GOAL := help
SSH_DIR := $(HOME)/.ssh
CONTAINER := php
COMMAND := /bin/bash

help: ## Affiche cette aide.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Démarre les conteneurs.
	@docker compose up -d

stop: ## Arrête les conteneurs.
	@docker compose stop

install: ssh-symlink ## Configure le projet et installe les dépendances.
	@echo "Configuration du projet..."
	@cp -n .env.example .env || true
	@mkdir -p documents
	@cp -n htdocs/conf/conf.php.example htdocs/conf/conf.php || true

	@echo "\nDémarrage de l'application..."
	@docker compose up -d

	@echo "\nInstallation des dépendances Composer..."
	@$(MAKE) composer-install

	@echo "\n------------------------------------------------"
	@echo "Installation terminée."
	@echo "------------------------------------------------\n"

exec: ## Exécute une commande dans le conteneur PHP (par défaut : /bin/bash).
	docker compose exec $(CONTAINER) $(COMMAND)

ssh-symlink: ## Crée un lien symbolique vers les clés SSH.
	@echo "Configuration des clés SSH..."
	@ln -sfn $(SSH_DIR) ./

composer-install: ## Installe les dépendances Composer.
	@$(MAKE) exec COMMAND="composer install"

composer-update: ## Met à jour les dépendances Composer.
	@$(MAKE) exec COMMAND="composer update"
