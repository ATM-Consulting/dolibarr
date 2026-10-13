# Dolibarr 24.0 — fork Koesio / Kali

Fork ATM Consulting du core Dolibarr **24.0** pour l'instance Kali (Koesio), avec backports de versions plus récentes et spécifiques client.

## Stack
- PHP 8, MySQL/MariaDB, jQuery (fourni par le core).
- Modules externes dans `htdocs/custom/` (dépôts séparés, ignorés par ce dépôt).

## Structure
- `htdocs/<module>/card.php`, `list.php` : écrans.
- `htdocs/core/lib/*.lib.php` : fonctions partagées ; `htdocs/core/js/` : JS statique (chargé via `$arrayofjs` de `llxHeader()`).
- `htdocs/langs/<lang>/*.lang` : traductions (`en_US` et `fr_FR` au minimum).
- `test/phpunit/` : tests PHPUnit du core (`CommonClassTest`).
- `ChangeLogKali.md` : liste des backports (`##BACKPORT`) et spécifiques Koesio (`## SPÉ KOESIO`). Le `ChangeLog` upstream n'est pas modifié.

## Remotes / branches
- `origin` = Dolibarr upstream, `atm` = ATM-Consulting/dolibarr.
- Branche de base : `24.0_koesio_kali`.
- Branches de travail : `NEW|FIX/<sprint>/<ticket>/<NomPascalCase>` depuis `atm/24.0_koesio_kali`.

## Commandes
- Lint : `php -l <fichier>` ; JS : `node --check <fichier>`.
- Tests : `cd test/phpunit && phpunit <Nom>Test.php` (utilise `htdocs/conf/conf.php` ; transaction annulée par `CommonClassTest`).
- Style : `phpcs --standard=dev/setup/codesniffer/ruleset.xml <fichier>`.

## Conventions
- Code backporté encadré par `/** START BACKPORT v25 : PR#<n> */` … `/** END BACKPORT */` (un couple par bloc modifié ; un seul autour des fonctions ajoutées dans une lib).
- Port fidèle de la PR upstream (mêmes noms de fonctions, constantes, classes CSS) pour faciliter le nettoyage à la prochaine MDV.
- Chaque backport ajoute une ligne `PR#<n> - <intitulé>` dans `##BACKPORT` de `ChangeLogKali.md` ; un spécifique ajoute `<commit> - <intitulé>` dans `## SPÉ KOESIO`.
- Pas de co-signature IA dans les commits/PR.

## Affichage des lots (T260251, PR#41643)
- Helpers `getBatchLinesToggleHeader()`, `getBatchLineToggleLink()`, `getBatchLineChildRowAttributes()`, `getBatchLotNomUrl()` (`core/lib/product.lib.php`) ; `getReceptionLineProductCell()`, `getReceptionLineQtyOrderedCells()` (`core/lib/reception.lib.php`) ; JS `core/js/lib_batchlines_toggle.js`.
- Écrans : `expedition/card.php`, `reception/card.php`, `product/stock/product.php`.
- Constantes cachées : `SHIPMENT_SHOW_ALL_BATCH_BY_DEFAULT`, `RECEPTION_SHOW_ALL_BATCH_BY_DEFAULT` (défaut 1), `STOCK_SHOW_ALL_BATCH_BY_DEFAULT`.
