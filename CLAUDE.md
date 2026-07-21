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

## Build / Test
- Tests unitaires : `test/phpunit/` (PHPUnit) — `phpunit -c test/phpunit/phpunittest.xml`
- e2e : Playwright (instance de test locale)
- Instance de dev : servie via `php -S` sur une DB de copie migrée

## Conventions
- Commits : préfixe MAJUSCULE + ` : ` (`NEW : …`, `FIX : …`, `REFACTOR : …`, `DOC : …`), sujet EN à l'impératif ≤72 car.
- Une branche par repo/modif ; ne jamais committer sur `13.0_ama` (v13 figée) ni cliama `2.5`.
- Patchs core encadrés par des commentaires `SPE AMA` / `FIN SPE AMA` explicites et minimaux.
- Règles Dolibarr : `$db->prefix()` (jamais `MAIN_DB_PREFIX`/`llx_`), `GETPOST($v,'type')`, `intval()`/`$db->escape()`, pas de `SELECT *`, `dol_syslog()` sur erreurs.
