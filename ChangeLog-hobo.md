# ChangeLog ATM — fork hobo (`atm/20.0_hobo`)

Suivi des correctifs et évolutions **spécifiques ATM** appliqués au fork hobo, en
complément du `ChangeLog` Dolibarr upstream (qui n'est pas modifié).

## 20.0.5 — hobo

### FIX
- **Holiday** : l'approbation d'une demande de congés était refusée à tort (« La création
  de cette demande de congé est bloquée car votre solde est négatif ») quand la demande
  contenait une demi-journée (début l'après-midi ou fin le matin) et que le solde
  couvrait exactement les jours demandés. Le contrôle du solde dans
  `Holiday::approve()` comptait la demande en jours entiers car il ne transmettait pas
  la demi-journée à `num_open_day()`. Elle est maintenant transmise (`halfday`), comme
  le fait déjà `holiday/card.php`. Concerne les types de congé avec « bloqué si solde
  négatif » (ex. congés payés). Backport du correctif upstream Dolibarr
  (PR #41396, commit a9c15cc9de5), limité à `approve()` : `validate()` et `update()`
  ne calculent pas de jours demandés en 20.0. _(ticket DA029073, branche
  `FIX/168/DA029073/backport-holiday-halfday-balance-check`)_

## 20.0.4 — hobo

### FIX
- **Holiday** : les pièces jointes et le badge de comptage d'une demande de congés
  affichaient aussi les fichiers d'autres demandes partageant le même dossier de
  stockage (`get_exdir()` niveau 2 basé sur l'id). Ajout d'un filtrage par préfixe
  de référence via la fonction utilitaire `holidayFilterOwnedFiles()`, utilisée dans
  `holiday_prepare_head()` et `holiday/document.php`. Les portions spécifiques ATM
  sont balisées `[ATM-MDV]` en vue de leur suppression en v23+ (le stockage y devient
  propre par référence). _(branche `FIX/CONFLICT_DOCUMENTS_HOLIDAYS`)_