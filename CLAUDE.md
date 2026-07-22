# CLAUDE.md — Dolibarr AMA (branche `23.0_ama`)

## Description
Fork Dolibarr d'ATM Consulting pour le client **AMA**. Cette branche `23.0_ama` part de
**Dolibarr 23.0.3 officiel** (tag `23.0.3`) et sert de socle à la montée de version depuis
`13.0_ama` (v13). Elle ne doit contenir QUE le **spécifique core irréductible** AMA — tout ce qui
peut l'être est re-implémenté en hooks/triggers dans le module `cliama` (dépôt séparé).

## Stack
- PHP / Dolibarr ERP 23.0.3
- MySQL/MariaDB
- Modules custom sous `htdocs/custom/` (dépôts git séparés ; `cliama` = spécifique AMA)

## Structure
- `htdocs/` — cœur Dolibarr (patchs AMA minimaux, marqués `SPE AMA`)
- `htdocs/custom/cliama/` — module spécifique AMA (workflow prépa/transit/location/retour), branche `3.0`
- `htdocs/ChangeLog-ATM.md` — journal des développements spécifiques AMA portés (par cible : `cliama` / `23.0_ama` / `core` / `drop`)

## Stratégie de migration (hybride)
Repartir de 23.0.3 officiel, ne PAS recopier les patchs core v13 à l'identique : reposer chaque
comportement sur un hook/trigger v23 (→ `cliama`) et ne garder en patch core (`23.0_ama`) que
l'irréductible sans point d'extension. Voir `htdocs/ChangeLog-ATM.md`.

## Patches core AMA appliqués sur cette branche (`SPE AMA`)
Chaque montée de version Dolibarr doit les **ré-appliquer**. Liste tenue à jour dans `htdocs/ChangeLog-ATM.md`.
- `htdocs/expedition/class/expedition.class.php` — `setClosed($notrigger = 0)` : permet de clôturer sans relancer le trigger `SHIPPING_CLOSED` (le workflow prépa/transit de `cliama` pilote ses propres mouvements de stock).
- `htdocs/core/class/conf.class.php` — le forçage v23 `STOCK_CALCULATE_ON_SHIPMENT_CLOSE=1` (déclenché par `productbatch`) est **gardé par la const `CLIAMA_MANAGE_SHIPMENT_STOCK`**. Sans ce garde, le core décrémente le stock depuis l'entrepôt d'origine à la clôture, en conflit avec `cliama` (régression v23 absente en v13). La const est posée par le descripteur `cliama` (`$this->const[]`).
- `htdocs/core/lib/pdf.lib.php` — `pdf_getlinedesc()` : (1) masque DLC/DLUO (`eatby`/`sellby`) sur tous les PDF de lignes (ticket DA021660) ; (2) ajoute le n° d'inventaire AMA à la suite du n° de lot/série, via `getInventoryCodeForBatch()` du module `cliama` (chargé dynamiquement par `dol_buildpath`, sans dépendance dure — patch inerte si `cliama` absent).

## Build / Test
- Tests unitaires : `test/phpunit/` (PHPUnit) — `phpunit -c test/phpunit/phpunittest.xml`
- e2e : Playwright (instance de test locale)
- Instance de dev : servie via `php -S` sur une DB de copie migrée

## Conventions
- Commits : préfixe MAJUSCULE + ` : ` (`NEW : …`, `FIX : …`, `REFACTOR : …`, `DOC : …`), sujet EN à l'impératif ≤72 car.
- Une branche par repo/modif ; ne jamais committer sur `13.0_ama` (v13 figée) ni cliama `2.5`.
- Patchs core encadrés par des commentaires `SPE AMA` / `FIN SPE AMA` explicites et minimaux.
- Règles Dolibarr : `$db->prefix()` (jamais `MAIN_DB_PREFIX`/`llx_`), `GETPOST($v,'type')`, `intval()`/`$db->escape()`, pas de `SELECT *`, `dol_syslog()` sur erreurs.
