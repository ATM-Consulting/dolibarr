# Changelog iseta — patchs spécifiques cœur Dolibarr

Modifications spécifiques iseta appliquées au cœur Dolibarr sur la branche `22.0_iseta`
(hors modules `htdocs/custom/`). Chaque changement est marqué en code par `BACKPORT PR #37329`
(ou une référence équivalente) pour être retrouvé lors des montées de version.

## 2026-07-16 — Backport OAuth Microsoft Exchange Online (PR Dolibarr #37329)

Backport de la fonctionnalité « Microsoft Exchange Online [SMTP/IMAP] OAuth2 » depuis la
branche 23.0 vers la 22.0.

- **Source** : https://github.com/Dolibarr/dolibarr/pull/37329 (mergée en 23.0)
- **Périmètre** : nouveau provider OAuth `MICROSOFT3` (scopes `outlook.office.com` :
  `SMTP.Send` + `IMAP.AccessAsUser.All`, `offline_access`), pour l'envoi/lecture de mail
  via OAuth2 sur Exchange Online.

### Fichiers ajoutés (copie conforme de la 23.0)
- `htdocs/includes/OAuth/OAuth2/Service/Microsoft3.php`
- `htdocs/core/modules/oauth/microsoft3_oauthcallback.php`

### Fichiers modifiés (hunks OAuth uniquement, jamais de copie complète)
- `htdocs/core/lib/oauth.lib.php` — enregistrement du provider `MICROSOFT3`
  (`getAllOauth2Array` + `getSupportedOauth2Array`)
- `htdocs/admin/oauth.php` — affichage du champ Tenant pour `MICROSOFT3`
- `htdocs/core/modules/oauth/microsoft_oauthcallback.php` — gestion du paramètre `state`
- `htdocs/core/class/CMailFile.class.php` — `setAuthMode('XOAUTH2')` en tête du bloc XOAUTH2
  + `dol_syslog` quand l'objet token est invalide
- `htdocs/langs/en_US/oauth.lang`, `htdocs/langs/fr_FR/oauth.lang` — clé `OAuthErrorStateDiffers`

### Volontairement EXCLUS de la PR (bugfixes sans rapport avec OAuth)
- `htdocs/compta/facture/card.php` (guard `fetch_thirdparty`)
- `htdocs/website/class/websitepage.class.php` (`$this->errors[]`)
- suppressions cosmétiques de lignes vides dans `CMailFile.class.php`

### Intégration
- Branche : `NEW/oauth/PR37329/MicrosoftExchangeOAuth` → PR vers `22.0_iseta` (remote `atm`).
