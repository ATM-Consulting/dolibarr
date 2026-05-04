# Standardisation du module `resource` au format Module Builder

**Date :** 2026-05-04
**Auteur :** Nicolas Vidal (ATM Consulting)
**Statut :** Spec validée — prêt pour planification d'implémentation
**Branche cible :** `21.0_atm`

---

## 1. Contexte

Le module cœur `dolibarr/htdocs/resource/` (gestion des ressources : salles, matériel, véhicules…) date d'avant la généralisation du format **Module Builder** (MB) de Dolibarr. Il présente plusieurs écarts avec le standard moderne :

- Classe métier `Dolresource` (1044 lignes) sans `$fields[]`, héritant de `CommonObject` mais sans utiliser ses méthodes génériques (`createCommon`, `updateCommon`, `fetchCommon`, etc.).
- Méthodes non standard mêlant CRUD ressource et logique de liaison `element_resource` (`fetchElementResources`, `getElementResources`, `update_element_resource`).
- Sous-pages (`card.php`, `list.php`, `note.php`, `document.php`, `agenda.php`, `contact.php`) au format pré-MB.
- Colonnes SQL héritées : `datec`, `fk_user_author`, `fk_statut` au lieu de `date_creation`, `fk_user_creat`, `status`.
- Pas de système de numérotation pluggable.
- Pas de droit `export`.

**Objectif :** réécrire intégralement le module au format MB strict, tout en préservant les données existantes et les points d'extension publics (hooks).

## 2. Décisions de cadrage

| # | Sujet | Décision |
|---|---|---|
| 1 | Emplacement | Module custom remplaçant dans `/htdocs/custom/resource/` |
| 2 | Périmètre | Skeleton MB pur — `element_resource.php` (linkage) reporté en **phase 2** |
| 3 | BC classe PHP | **Rupture nette** — suppression de `Dolresource`, documentée dans `CHANGELOG.md` |
| 4 | Migration SQL | Renommage des colonnes au standard MB, exécuté via `sql/migration/*.sql` au `/install/upgrade.php` |
| 5 | Sous-pages | Toutes régénérées au format MB strict de Dolibarr 21 |
| 6 | Numérotation | Numérotation auto pluggable (`mod_resource_standard`) avec option de saisie manuelle |
| 7 | Permissions | `read` / `write` / `delete` (IDs cœur conservés) + nouveau `export` (ID 41124) |
| 8 | Hooks | **BC préservée** sur les contextes (`resourcecard`, `resourcelist`, `resourcedao`) |
| 9 | Tests | PHPUnit (classe métier) + plan de test manuel pour migration SQL et UI |
| 10 | Classe | `class Resource extends CommonObject` dans `class/resource.class.php` |

## 3. Architecture cible

### 3.1 Stratégie de remplacement

Le module custom déclare `numero = 520` et `rights_class = 'resource'` — identiques au module cœur. La constante `MAIN_MODULE_RESOURCE` sert de verrou unique : un seul des deux modules peut être actif à la fois.

**Procédure de bascule** (documentée dans le `README`) :
1. Désactiver le module cœur depuis l'admin Dolibarr.
2. Déployer `/htdocs/custom/resource/`.
3. Activer le module custom depuis l'admin → exécution de `init()` → exécution des migrations SQL.
4. Données et permissions préservées.

### 3.2 Arborescence

```
custom/resource/
├── core/
│   ├── modules/
│   │   ├── modResource.class.php                                         # descripteur
│   │   └── resource/
│   │       ├── modules_resource.php                                      # ModeleNumRefResource (abstract)
│   │       └── mod_resource_standard.php                                 # impl. par défaut
│   └── triggers/
│       └── interface_99_modResource_ResourceTriggers.class.php
├── class/
│   ├── resource.class.php                                                # class Resource extends CommonObject
│   └── html.formresource.class.php                                       # FormResource (formulaires)
├── lib/
│   └── resource.lib.php                                                  # resource_prepare_head() + helpers
├── sql/
│   ├── llx_resource.sql                                                  # CREATE TABLE IF NOT EXISTS (format MB)
│   ├── llx_resource.key.sql
│   ├── llx_resource_extrafields.sql
│   ├── llx_resource_extrafields.key.sql
│   └── migration/
│       └── 21.0.0-resource-modulebuilder.sql                             # rename colonnes
├── img/
│   └── resource.svg
├── langs/
│   ├── fr_FR/resource.lang                                               # copié depuis le cœur + nouveaux libellés
│   └── en_US/resource.lang
├── admin/
│   └── setup.php                                                         # config numérotation + RESOURCE_REF_AUTO
├── test/phpunit/
│   └── ResourceTest.php
├── card.php
├── list.php
├── note.php
├── document.php
├── agenda.php
├── contact.php
├── README.md
├── CHANGELOG.md
└── MIGRATION_TEST_PLAN.md
```

## 4. Classe métier `Resource`

### 4.1 Signature

```php
class Resource extends CommonObject
{
    public $module        = 'resource';
    public $element       = 'resource';
    public $table_element = 'resource';
    public $picto         = 'resource';
    public $ismultientitymanaged = 1;
    public $isextrafieldmanaged  = 1;

    const STATUS_DRAFT     = 0;
    const STATUS_VALIDATED = 1;
    const STATUS_DISABLED  = 9;
}
```

### 4.2 Tableau `$fields`

Tous les champs sont déclarés dans `$fields[]` ; toute la logique CRUD/list/search est générée à partir.

| Champ | Type | Notes |
|---|---|---|
| `rowid` | integer | PK auto-increment |
| `entity` | integer | multi-entity, default 1 |
| `ref` | varchar(255) | not null, `showoncombobox=1`, `searchall=1` |
| `asset_number` | varchar(255) | `searchall=1` |
| `fk_code_type_resource` | `sellist:c_type_resource:label:code::active=1` | combobox dictionnaire |
| `description` | text | |
| `address` / `zip` / `town` | varchar | |
| `fk_country` | `integer:Ccountry:core/class/ccountry.class.php` | |
| `fk_state` | `integer:State:core/class/cstate.class.php` | |
| `phone` / `email` / `url` | varchar / mail / url | |
| `max_users` | integer | |
| `photo_filename` | varchar(255) | |
| `note_public` / `note_private` | html | |
| `date_creation` / `date_valid` / `tms` | datetime / timestamp | colonnes audit standard |
| `fk_user_creat` / `fk_user_modif` / `fk_user_valid` | `integer:User:user/class/user.class.php` | |
| `import_key` / `extraparams` | varchar | |
| `status` | smallint | `arrayofkeyval` = `[0=>Draft, 1=>Validated, 9=>Disabled]` |

### 4.3 Méthodes publiques

| Méthode | Rôle |
|---|---|
| `__construct(DoliDB $db)` | init `$fields`, traductions |
| `create(User $user, $notrigger = 0)` | délègue à `createCommon()`, gère ref auto si `RESOURCE_REF_AUTO=1` |
| `update(User $user, $notrigger = 0)` | délègue à `updateCommon()` |
| `delete(User $user, $notrigger = 0)` | délègue à `deleteCommon()` |
| `fetch($id, $ref = null)` | délègue à `fetchCommon()` |
| `fetchAll(...)` | standard MB (pagination, filtres, tri) |
| `validate(User $user, $notrigger = 0)` | `STATUS_DRAFT → STATUS_VALIDATED`, set `date_valid`, `fk_user_valid`, trigger `RESOURCE_VALIDATE` |
| `setDraft(User $user, $notrigger = 0)` | retour à `STATUS_DRAFT`, trigger `RESOURCE_UNVALIDATE` |
| `getNomUrl(...)` | lien HTML standard MB |
| `getLibStatut($mode)` / `LibStatut($s, $mode)` | rendu badge |
| `initAsSpecimen()` | données factices pour aperçu PDF |
| `getNextNumRef()` | délègue au modèle de numérotation actif |

### 4.4 Méthodes supprimées (rupture BC)

`fetchAll(...)` (signature non standard), `fetchElementResources()`, `getElementResources()`, `update_element_resource()`, `fetch_element_resource()` → reportées en **phase 2** (chantier dédié `element_resource`).

## 5. Migration SQL

**Fichier :** `sql/migration/21.0.0-resource-modulebuilder.sql`

```sql
-- Renommage colonnes au standard ModuleBuilder
ALTER TABLE llx_resource CHANGE COLUMN datec          date_creation datetime DEFAULT NULL;
ALTER TABLE llx_resource CHANGE COLUMN fk_user_author fk_user_creat integer  DEFAULT NULL;
ALTER TABLE llx_resource CHANGE COLUMN fk_statut      status        smallint NOT NULL DEFAULT 0;

-- Backfill défensif date_creation NULL
UPDATE llx_resource
   SET date_creation = COALESCE(date_creation, tms, NOW())
 WHERE date_creation IS NULL;

ALTER TABLE llx_resource MODIFY COLUMN date_creation datetime NOT NULL;
```

**Idempotence :** Dolibarr `run_sql()` ignore les erreurs `42S22` (colonne inexistante) si la requête est annotée selon la convention. Une seconde exécution est un no-op silencieux.

**Install neuve :** `sql/llx_resource.sql` utilise `CREATE TABLE IF NOT EXISTS` avec déjà les noms standards. Aucune migration jouée.

**Plan de test manuel** dans `MIGRATION_TEST_PLAN.md` :
1. Restaurer un dump prod v21.0 (avec anciens noms).
2. Activer le module custom → vérifier `DESCRIBE llx_resource`.
3. Vérifier `SELECT COUNT(*) FROM llx_resource WHERE date_creation IS NULL` = 0.
4. Ouvrir une ressource existante → valeurs identiques.
5. Rejouer la migration → no-op.

## 6. Numérotation pluggable

**Classe abstraite** `core/modules/resource/modules_resource.php` :

```php
abstract class ModeleNumRefResource
{
    public $error = '';
    abstract public function info($langs);
    abstract public function getExample();
    abstract public function getNextValue($object);
    abstract public function canBeActivated($object);
}
```

**Modèle par défaut** `mod_resource_standard.php` :

```php
class mod_resource_standard extends ModeleNumRefResource
{
    public $name = 'standard';
    public $prefix = 'RES';

    public function getNextValue($object)
    {
        global $db;
        $mask = getDolGlobalString('RESOURCE_STANDARD_MASK', $this->prefix.'{yy}{mm}-{0000}');
        require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
        return get_next_value($db, $mask, 'resource', 'ref', '', null, dol_now());
    }

    public function getExample() { return 'RES2605-0001'; }
}
```

**Constantes module** :

| Const | Défaut | Rôle |
|---|---|---|
| `RESOURCE_ADDON` | `mod_resource_standard` | nom du modèle actif |
| `RESOURCE_STANDARD_MASK` | `RES{yy}{mm}-{0000}` | masque numéro |
| `RESOURCE_REF_AUTO` | `1` (install neuve) / `0` (upgrade) | ref auto vs manuelle |

**Logique dans `Resource::create()`** :

```php
public function create(User $user, $notrigger = 0)
{
    if (getDolGlobalInt('RESOURCE_REF_AUTO', 1) && (empty($this->ref) || $this->ref === 'auto')) {
        $this->ref = $this->getNextNumRef();
    }
    if (empty($this->ref)) {
        $this->error = 'ErrRefRequired';
        return -1;
    }
    return $this->createCommon($user, $notrigger);
}
```

## 7. Module descriptor

**Fichier :** `core/modules/modResource.class.php`

Points clés :
- `$numero = 520` ; `$rights_class = 'resource'` ; `$const_name = 'MAIN_MODULE_RESOURCE'` (identiques au cœur → bascule mutuellement exclusive)
- `$family = 'projects'` ; `$module_position = '36'`
- `$module_parts['hooks']` = `['resourcecard', 'resourcelist', 'resourcedao']` (BC)
- `$langfiles = ['resource']`
- 3 constantes `RESOURCE_ADDON` / `RESOURCE_STANDARD_MASK` / `RESOURCE_REF_AUTO`
- 4 droits :
  - 41121 / `read`
  - 41122 / `write`
  - 41123 / `delete`
  - 41124 / `export` (nouveau)
- `init()` charge `sql/` (tables + migrations + clés) via `_load_tables()`

### 7.1 Menus

3 entrées **left menu** sous le top `agenda` (mêmes ancres que le cœur) :

| Position | Titre | URL | `perms` | `enabled` |
|---|---|---|---|---|
| 100 | `MenuResourceIndex` (avec picto) | `/custom/resource/list.php` | `$user->hasRight('resource','read')` | `$conf->resource->enabled` |
| 101 | `MenuResourceAdd` | `/custom/resource/card.php?action=create` | `$user->hasRight('resource','write')` | `$conf->resource->enabled` |
| 102 | `List` | `/custom/resource/list.php` | `$user->hasRight('resource','read')` | `$conf->resource->enabled` |

Pas d'entrée `top` : on s'accroche au top `agenda` existant.

## 8. Sous-pages

### 8.1 `lib/resource.lib.php`

Une seule fonction publique `resource_prepare_head(Resource $object): array` qui retourne la structure d'onglets standard (Card / Contacts / Notes / Documents / Events) avec compteurs dynamiques (`<span class="badge">`), et appelle `complete_head_from_modules()` pour les onglets ajoutés par les modules tiers.

### 8.2 `card.php`

Thin controller :
- charge `Resource`, `ExtraFields`, helpers
- `restrictedArea($user, 'resource', $object->id, 'resource')`
- inclut les action handlers standards : `actions_addupdatedelete.inc.php`, `actions_setnotes.inc.php`, `actions_builddoc.inc.php`
- gère `confirm_validate` et `confirm_setdraft` localement
- rendu via `dol_get_fiche_head()` + `$object->showOutputFields()` + boutons d'action standards

### 8.3 `list.php`

Pattern MB **list builder via `$fields`** :
- filtres dérivés de `$fields[]` (visible >= 0, searchall=1)
- pagination/tri/colonnes choisies par l'utilisateur
- bouton "Ajouter" si `write`
- bouton "Exporter" si `export` (nouveau droit)

### 8.4 `note.php` / `document.php` / `agenda.php` / `contact.php`

Templates MB stricts (≈ 80 lignes chacun), alignés sur les modules MB-générés du cœur (`recruitmentcandidature_*.php`, `conferenceorboothattendee_*.php`).

## 9. Hooks et triggers

### 9.1 Hooks (BC)

Les contextes existants sont préservés : `resourcecard`, `resourcelist`, `resourcedao`. Tous les `executeHooks()` standards sont appelés (`formObjectOptions`, `printSearchForm`, `addMoreActionsButtons`, `addMoreEntries`, `printFieldListWhere`, etc.). Les modules tiers déjà branchés continuent de fonctionner sans modification.

### 9.2 Triggers

Trigger handler unique : `core/triggers/interface_99_modResource_ResourceTriggers.class.php`. Émet :
- `RESOURCE_CREATE`
- `RESOURCE_MODIFY`
- `RESOURCE_DELETE`
- `RESOURCE_VALIDATE`
- `RESOURCE_UNVALIDATE`

Aucune logique métier ; le handler est un point d'écoute pour modules tiers.

## 10. Tests

### 10.1 PHPUnit — `test/phpunit/ResourceTest.php`

Bootstrap Dolibarr standard (cf. `SocieteTest.php`). Cas couverts :

- `testCreate()` — create + fetch + asserts champs
- `testCreateWithAutoRef()` — `RESOURCE_REF_AUTO=1` → ref auto
- `testCreateWithManualRef()` — `RESOURCE_REF_AUTO=0` → ref obligatoire
- `testUpdate()` — `tms` et `fk_user_modif` mis à jour
- `testValidateSetDraft()` — transitions de statut
- `testGetNomUrl()` — lien HTML correct
- `testFetchAll()` — filtres / tri / pagination
- `testDelete()` — suppression + cleanup
- `testFetchTypeResourceLink()` — sellist `c_type_resource`
- `testGetNextNumRefStandard()` — masque par défaut

Cible : ≥ 80 % de couverture sur `Resource`.

### 10.2 `MIGRATION_TEST_PLAN.md`

Scénarios manuels :
1. Migration colonnes (cf. §5)
2. Bascule core → custom (procédure de désactivation/activation)
3. Card : create/edit/validate/delete via UI
4. List : filtres / tri / export CSV (nouveau droit)
5. Onglet contacts : ajout / suppression
6. Onglet documents : upload / download / suppression
7. Onglet agenda : création événement lié
8. Module désactivé puis réactivé : pas de perte de données ni de menus
9. Modules tiers consommateurs (booking, workstation, actioncomm) : non régression

## 11. CHANGELOG (rupture BC)

Documente :
- Suppression de la classe `Dolresource` → remplacée par `Resource`.
- Suppression des méthodes liées à `element_resource` (reportées phase 2).
- Renommage colonnes : `datec` → `date_creation`, `fk_user_author` → `fk_user_creat`, `fk_statut` → `status`.
- Nouveau droit : `resource->export` (ID 41124).
- Nouvelles constantes : `RESOURCE_ADDON`, `RESOURCE_STANDARD_MASK`, `RESOURCE_REF_AUTO`.
- Hooks (contextes + executeHooks) : **inchangés** — pas d'action requise pour les modules tiers.

## 12. Hors-scope (phase 2 future)

- Standardisation de `element_resource.php` et de la table `llx_element_resources`.
- Réécriture des méthodes `fetchElementResources`, `getElementResources`, `update_element_resource` au format MB (probablement dans une classe dédiée `ResourceLink` ou `ElementResource`).
- Ajout éventuel de fonctionnalités (calendrier de disponibilité, tarifs, types éditables).

## 13. Critères d'acceptation

- Le module custom s'active sans erreur sur une base v21.0 contenant des ressources existantes.
- Toutes les ressources existantes restent visibles, modifiables, supprimables.
- Le hook `resourcecard` continue d'être déclenché sur la card (vérifié avec un module tiers de test).
- Tous les tests PHPUnit passent.
- Le plan de test manuel est entièrement validé.
- La désactivation puis réactivation du module ne provoque aucune perte de données.
- Le `CHANGELOG.md` documente toutes les ruptures BC.
