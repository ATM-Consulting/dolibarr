# 🚀 Installation du projet Dolibarr avec Docker

Ce guide explique comment installer et lancer le projet en local avec Docker.

---

## ✅ Prérequis
- Docker & Docker Compose installés
- Une base de données fournie
- Accès SSH au serveur Monk/Leaseboard preprod ou prod

---
# Monk (miroir local de la prod)
Cet environnement permet de lancer une instance locale de Monk (Dolibarr) pour tester Fink ou reproduire le comportement de la prod.

## 🛠️ Étapes d'installation 

### 1. Récupérer le code de la prod
On synchronise le code depuis la prod ou la preprod, en excluant les documents. 

En configurant votre .env puis :
```bash
rsync -azP --delete --exclude "documents" user@ip-monk:/home/atm/monk/ monk/
```
ip-monk = Preprod : 10.200.54.243 / Prod : 10.200.55.243

### 2. Lancer les conteneurs
```bash
make up-monk
```

### 3. Créer la base de données
Chaque instance (Leaseboard, Monk) dispose de son propre conteneur MariaDB, avec une base dolibarr créée automatiquement au démarrage.
Il suffit donc d’importer votre dump dans cette base.

```bash
mysql -h 127.0.0.1 -P 3310 -u root -p dolibarr < dump_monk.sql
```

### 4. Installer dolibarr
Commencer par supprimer le fichier de configuration récupéré par le rsync :
```bash
rm htdocs/conf/conf.php
```
Puis : 
http://localhost:8090/install
```bash
Configuration Base de données :
Nom de la base de données : dolibarr
Serveur de base de données : database
Port : 3306
Identifiant : root
Mot de passe : root
```
### 5. Executer les requêtes pour preparer l'environnement local
Exécuter le script dans doli-cli-cpro/script/prepareEnvironment/dev/3.variousSqlRequests.sql (requêtes sql permettant de désactiver le mode prod, désactiver les mails, changer la couleur du header, et de la redirection OKTA)

-------------

## 🛠️ Étapes d'installation pour Leaseboard

### 1. Récupérer le code de la prod
On synchronise le code depuis la prod ou la preprod, en excluant les documents.

En configurant votre .env puis :
```bash
rsync -azP --delete --exclude "documents" user@ip-leaseboard:/home/atm/leaseboard/ leaseboard/
```
ip-leaseboard = Preprod : 10.200.54.241 / Prod : 10.200.55.241


### 2. Démarrer les conteneurs

```bash
make up-leaseboard
```

### 3. Importer un dump SQL 
Importer un dump :

```bash
mysql -h 127.0.0.1 -P 3306 -u root -p dolibarr < dump_leaseboard.sql
```

### 4. Installer dolibarr 

http://localhost:8088/install

### 5. Executer les requêtes pour preparer l'environnement local

## Configurer son utilisateur (dolibarr)
- Se donner toutes les permissions
- S'ajouter à un groupe

## Commandes utiles 

```bash
make up-leaseboard     # Démarrer Leaseboard
make up-monk           # Démarrer Monk
make stop              # Stopper les conteneurs
make exec-php-l        # Terminal PHP Leaseboard
make exec-php-m        # Terminal PHP Monk
```
