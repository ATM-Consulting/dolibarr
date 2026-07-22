# ChangeLog — Développements spécifiques AMA (branche Dolibarr core)

Ce fichier liste les développements spécifiques AMA présents sur la **branche core Dolibarr**
du client (`13.0_ama`), c'est-à-dire les patches appliqués au cœur de Dolibarr — **hors module
`cliama`**, qui est versionné dans son propre dépôt.

Référence du diff : `13.0_ama` vs Dolibarr **13.0 officiel** (merge-base `7393abea`) →
32 fichiers, 64 commits. La colonne **Cible v23** indique la destination retenue pour la montée
de version 23.0.3 (stratégie hybride) : `cliama` (re-implémentation via hook/trigger),
`23.0_ama` (patch core irréductible conservé dans une branche core forkée fine), `core`
(correctif générique contribuable upstream), `drop` (non conservé / absorbé par le standard v23).

> Inventaire détaillé + réconciliation avec la liste CDP : voir
> `~/Claude/dolibarr/audit/inventaire-devs-specifiques-branche-dolibarr.csv`.

---

## Expédition / Livraison

### Sélection multi-lots / multi-séries à l'expédition — Cible v23 : cliama
- fichiers : `htdocs/expedition/card.php`, `htdocs/expedition/class/expeditionbatch.class.php`
- affecte plusieurs lots / n° de série à une même ligne d'expédition (boucle multi-batch,
  sélecteur AJAX `SpeAmaFormProduct`, suppression d'un lot par qté 0)
- ⚠️ `expeditionbatch.class.php` est **supprimé en v23** → réécriture complète via
  `ExpeditionLineBatch`. `fetchByExpDetSerial()` à durcir (injection SQL, `SELECT *`, pas de `free()`).

### Anti-sur-allocation du stock entre lignes d'expédition — Cible v23 : 23.0_ama
- fichier : `htdocs/expedition/card.php` (`$alreadyQtySetted` / `$alreadyQtyBatchSetted`)
- cumule la quantité déjà allouée par produit/entrepôt/lot sur les lignes précédentes pour ne
  jamais suggérer plus que le stock réel quand un produit est présent sur plusieurs lignes
- aucun point d'extension v23 → patch core. Nouvelle clé `StockQuantitiesAlreadyAllocatedOnPreviousLines`.

### Clôture d'expédition sans déclenchement de trigger (`setClosed($notrigger)`) — Cible v23 : 23.0_ama ✅ APPLIQUÉ
- fichier : `htdocs/expedition/class/expedition.class.php` (`setClosed()`)
- ajoute un paramètre `$notrigger` pour clôturer sans relancer `SHIPPING_CLOSED` (pilotage du
  workflow transit / stock locatif)
- v23 : `setClosed()` n'avait pas de `$notrigger` → **patch appliqué** sur `23.0_ama`
  (`setClosed($notrigger = 0)` + garde `if (!$error && !$notrigger)` sur `call_trigger('SHIPPING_CLOSED')`).

### Neutralisation du décrément stock standard à l'expédition (`conf.class.php`) — Cible v23 : 23.0_ama ✅ APPLIQUÉ *(nouveau patch v23)*
- fichier : `htdocs/core/class/conf.class.php`
- **Régression v23** (absente en 13.0_ama) : quand `productbatch` est activé, le core v23 **force**
  `STOCK_CALCULATE_ON_SHIPMENT_CLOSE=1` → décrément stock standard depuis l'entrepôt d'origine de
  l'expédition à la clôture, en **conflit** avec le workflow cliama (qui a déjà déplacé le stock
  vers l'entrepôt de transit). Vérité terrain : la prod v13 n'a **aucun** mouvement « classified closed ».
- **patch appliqué** : le forçage SHIPMENT/CLOSE est gardé par `if (empty(CLIAMA_MANAGE_SHIPMENT_STOCK))`,
  restaurant le comportement v13 (cliama gère 100 % des mouvements d'expédition).
- const `CLIAMA_MANAGE_SHIPMENT_STOCK=1` enregistrée au descripteur `cliama` (posée à l'install/upgrade).
- découvert par l'e2e d'intégration (invisible à l'audit statique). Détail : `~/Claude/dolibarr/audit/audit-flux-expedition-cliama-v23.md`.

### Trigger `LINESHIPPINGBATCH_INSERT` — Cible v23 : cliama
- fichier : `htdocs/expedition/class/expeditionbatch.class.php` (`create()`)
- point d'extension déclenché à la création d'un lot d'expédition (mouvement stock / affectation série)
- devient un trigger du module `cliama` lors de la réécriture `ExpeditionLineBatch`.

### Extrafields du bon de livraison — Cible v23 : drop
- fichier : `htdocs/delivery/class/delivery.class.php` (`create()`, `create_from_shipment()`)
- `insertExtraFields()` + propagation `array_options` expédition → BL
- **Requalifié** : ce diff provient d'un **merge d'officiel** (code Dolibarr 13.0.x), ce n'est **pas**
  un dev AMA. De plus natif en v23 (lignes 312 / 591 / 631) → rien à porter.

## Stock / Réappro / Mouvements

### Transfert en masse d'équipement sérialisé (+ date/heure du mouvement) — Cible v23 : cliama
- fichier : `htdocs/product/stock/massstockmove.php`
- écran de transfert de masse : n° série source/cible, type de transfert, utilisateur, n° inventaire,
  date ; refus des produits composés
- dépend de `AssetTranfert` + `cliama/script/interface.php` (dépôt séparé). ⚠️ CDP marque ce dev
  `Non conservé` **mais le code est actif et récemment maintenu** → à confirmer avec le client.
  La date saisie n'affecte que l'enregistrement asset, pas le mouvement Dolibarr réel.

### Blocage transfert entrepôt « neuf » → « occasion » — Cible v23 : cliama
- fichier : `htdocs/product/stock/massstockmove.php`
- interdit le transfert d'un produit non sérialisé d'un entrepôt catégorie « neuf » vers « occasion »
  (constantes `CLIAMA_NEW_WAREHOUSE_CATEGORY` / `CLIAMA_USED_WAREHOUSE_CATEGORY`)
- clé `NewCategoryCantGoToUsed` (présente en `fr_FR` uniquement).

### Contrôle de stock + sélecteur de lot à l'ajout (transfert masse) — Cible v23 : 23.0_ama + cliama
- fichier : `htdocs/product/stock/massstockmove.php`
- liste déroulante de lot alimentée en AJAX (entrepôt/produit), quantité en `input number` bornée
  au stock disponible, contrôle de disponibilité dès l'ajout de ligne
- contrôle qty/stock = 23.0_ama (générique) ; sélecteur AJAX = cliama.

### Refonte du traitement `createmovements` (anti-stock négatif) — Cible v23 : 23.0_ama
- fichier : `htdocs/product/stock/massstockmove.php` (`action=createmovements`)
- pré-contrôle du stock suffisant par ligne, arrêt + rollback à la première erreur, libellé de
  mouvement rendu non obligatoire, redirection finale vers `movement_list.php`
- v23 ne pré-contrôle pas le stock en masse → pertinent. Le pré-check anti-négatif est contribuable upstream.

### Réapprovisionnement multi-entrepôts + seuils par entrepôt — Cible v23 : cliama
- fichier : `htdocs/product/stock/replenish.php`
- filtre multi-entrepôts et utilisation du stock désiré / seuil d'alerte par entrepôt (au lieu des
  seuils globaux), via le hook maison `cliamaReplaceSQL` (remplace la requête)
- ⚠️ CDP marque `Non conservé` **mais le code est actif** → à confirmer.

### Liste des mouvements : filtre projet + extrafields + hooks — Cible v23 : drop
- fichier : `htdocs/product/stock/movement_list.php`
- filtre par projet, colonnes extensibles, hook `printFieldListValue`
- **natif en v23** → drop (reporting détaillé = Métabase côté CDP).

### Onglet stock produit : détail des lots repliable — Cible v23 : drop
- fichiers : `htdocs/product/stock/product.php`, `htdocs/admin/stock.php`
- collapse/expand du détail des lots par entrepôt + constante `STOCK_SHOW_ALL_BATCH_BY_DEFAULT`
- **natif en v23** (contribué upstream par ATM) → drop.

## Produit / Composition

### Composant optionnel / de base dans un kit (produit composé) — Cible v23 : 23.0_ama
- fichiers : `htdocs/product/class/product.class.php` (`add_sousproduit`, `update_sousproduit`,
  `is_sousproduit`, `getChildsArbo`), `htdocs/product/composition/card.php`
- colonne `optional` sur `product_association` + flag « optionnel / de base » exposé jusqu'à `getChildsArbo`
- clé `ComposedProductOptional`. ⚠️ `var_dump($qty)` en production (`composition/card.php:617`) à retirer.
  Le consommateur du flag (explosion du kit à la commande) est dans `cliama` (trigger LINEORDER_INSERT).

### Filtre « unité » sur la liste des produits — Cible v23 : 23.0_ama
- fichier : `htdocs/product/list.php`
- filtre par unité de mesure (`selectUnits`). Non marqué SPÉ AMA, utilité à confirmer.

### Autocomplete produit : pas d'auto-sélection du candidat unique — Cible v23 : 23.0_ama
- fichier : `htdocs/core/class/html.form.class.php` (`selectProducts()`, `$autoselect=0`)
- ne pré-sélectionne plus le premier produit proposé dans l'autocomplétion. Transverse (tous les
  sélecteurs produit) → contribuable upstream comme option.

## Réception / Dispatch fournisseur

### Réception : code inventaire + fournisseur reportés sur la fiche série — Cible v23 : cliama
- fichier : `htdocs/reception/card.php`
- copie le code inventaire / fournisseur / PA saisis à la réception vers le `ProductLot`
- le report d'extrafields par ligne est **natif en v23** (`getOptionalsFromPost`) ; la copie vers le
  `ProductLot` reste un trigger cliama (`RECEPTION_VALIDATE` / `ORDER_SUPPLIER_DISPATCH`).

### Réception : UX lignes série (masquage qty 0, saisie numérique bornée) — Cible v23 : cliama
- fichiers : `htdocs/reception/card.php`, `htdocs/fourn/commande/dispatch.php`
- masque les lignes de produit sérialisé à qté 0, borne la saisie de quantité. À réévaluer vs
  réception refondue v23.

### Dispatch : inversion volontaire DLC/DLUO (eatby/sellby) — Cible v23 : 23.0_ama
- fichier : `htdocs/fourn/commande/dispatch.php`
- inverse le mapping DLC ↔ eatby / DLUO ↔ sellby pour coller à l'usage AMA des champs
- ⚠️ **décision métier** : garder l'inversion ou revenir au natif ? Impacte le sens des dates en base.

### Fix datepicker sur clonage de ligne de dispatch — Cible v23 : core
- fichier : `htdocs/fourn/js/lib_dispatch.js.php` (`addDispatchLine()`)
- détruit/reconstruit les datepickers clonés (split de ligne batch) cassés par le clone jQuery.
  Bug fix générique, contribuable upstream.

## Configuration / Cœur transverse

### Modes de calcul de stock posés à l'activation (non forcés à chaque page) — Cible v23 : 23.0_ama
- fichiers : `htdocs/core/class/conf.class.php`, `htdocs/core/modules/modProductBatch.class.php`
- retire le forçage des constantes `STOCK_CALCULATE_ON_*` à chaque chargement de page ; posées une
  seule fois à l'activation du module (donc modifiables ensuite)
- ⚠️ bloc dupliqué à l'identique dans `modProductBatch::init()` → à dédoublonner au portage.

### PDF : retrait des dates eatby/sellby (DLC/DLUO) — Cible v23 : 23.0_ama
- FIX ticket DA021660 — 23/03/2022
- fichier : `htdocs/core/lib/pdf.lib.php`, fonction `pdf_getlinedesc`
- retrait des informations eatby et sellby des PDF (BL / Shipping Invoice)
- alternative v23 : constante `PRODUCT_DISABLE_*` ou modèle PDF dédié cliama.

### PDF : n° d'inventaire (code-barre) sur la description de ligne — Cible v23 : 23.0_ama
- fichier : `htdocs/core/lib/pdf.lib.php`, fonction `pdf_getlinedesc`
- ajoute le n° d'inventaire à la suite du n° de série (`getInventoryCodeForBatch` via `cliama.lib`)
- clé `InventoryNumber`. Idéalement déplacer le point d'injection dans un modèle PDF cliama.

## Non conservés / bruit (à ne pas porter)

### Type de catégorie « stock » pour entrepôts — Cible v23 : drop
- fichier : `htdocs/categories/class/categorie.class.php` (`TYPE_STOCK`)
- doublon avec le type natif `warehouse` ; CDP : « remplacé par inventaire+ ». À confirmer mort.

### Script one-shot de sérialisation des produits (MEP Ameublys) — Cible v23 : drop
- fichier : `htdocs/install/script_one_shoot_mep_ameublys.php`
- passe tous les produits (`fk_product_type=0`) à `status_batch=1`. **One-shot mort, ne jamais rejouer.**

### Divers correctifs absorbés par le standard v23 — Cible v23 : drop
- `htdocs/reception/card.php` (`$extralabelslines`), `htdocs/core/tpl/admin_extrafields_view.tpl.php`
  (guard `$mc`), `htdocs/core/class/extrafields.class.php` (ligne vide),
  `htdocs/accountancy/index.php` (`dol_fiche_head`) → natifs / bruit en v23.

## Développements abandonnés / revertés (historique, absents du tip)

> Détectés en croisant les **commits AMA** avec l'état courant (le diff `merge-base...tip` seul les
> masque, à cause d'un historique de merges criss-cross avec l'officiel 13.0.x).

### Onglet « détail des lots » + gestion des lots sur préparation brouillon — Cible v23 : drop (abandonné)
- fichiers : `htdocs/expedition/detail_batch.php` (supprimé), `htdocs/core/lib/sendings.lib.php`
- développé avr.→juil. 2021 (commits `51e2d76` « new detail batch tab beginning » … `36187de` « ajout
  équipement depuis fiche brouillon »), puis **reverté** par `276ef4b` « remove tab detail and go back
  on standard » (2021-07-30). Absent du tip → ne pas porter (décision « retour au standard » assumée).
- Autres fichiers AMA-authored revenus au standard par merge (aucun contenu AMA sur le tip) :
  `htdocs/livraison/class/livraison.class.php`, `htdocs/product/stock/stockatdate.php`.

## Traductions
- clés introduites : `ComposedProductOptional`, `ToDefine`, `DefineBatch`, `NewBatch`, `NoBatch`,
  `DetailBatch`, `ShowAllBatchByDefault`, `CollapseBatchDetailHelp`, `NotEnoughStock`, `EmptyStock`,
  `NewCategoryCantGoToUsed` (fichiers `langs/fr_FR` + `langs/en_US`)
- ⚠️ `NewCategoryCantGoToUsed` a une valeur française dans le fichier `en_US` → à corriger.
