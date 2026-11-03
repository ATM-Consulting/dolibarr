# ChangeLog ATM — fork hobo (`atm/20.0_hobo`)

Suivi des correctifs et évolutions **spécifiques ATM** appliqués au fork hobo, en
complément du `ChangeLog` Dolibarr upstream (qui n'est pas modifié).

## 20.0.5 — hobo

### FIX
- **Holiday** : l'approbation d'une demande de congés était refusée à tort (« La création
  de cette demande de congé est bloquée car votre solde est négatif ») alors que le solde
  couvrait exactement les jours demandés. Deux causes dans le contrôle du solde de
  `Holiday::approve()` (types de congé « bloqué si solde négatif », ex. congés payés) :
  la demi-journée (début l'après-midi ou fin le matin) n'était pas transmise à
  `num_open_day()`, et les dates étaient lues dans le fuseau du serveur (Europe/Paris) au
  lieu de l'heure universelle, ce qui décalait la période d'un jour (ex. 25/12 00:00 Paris
  = 24/12 23:00 UTC). Le contrôle utilise maintenant `date_debut_gmt`, `date_fin_gmt` et
  `halfday`, comme `holiday/card.php`. Backport du correctif upstream Dolibarr
  (PR #41396, commit a9c15cc9de5) limité à `approve()` : `validate()` et `update()` ne
  calculent pas de jours demandés en 20.0. _(ticket DA029073, branches
  `FIX/168/DA029073/backport-holiday-halfday-balance-check` puis
  `FIX/168/DA029073/holiday-approve-utc-dates`)_

## 20.0.4 — hobo

### FIX
- **Holiday** : les pièces jointes et le badge de comptage d'une demande de congés
  affichaient aussi les fichiers d'autres demandes partageant le même dossier de
  stockage (`get_exdir()` niveau 2 basé sur l'id). Ajout d'un filtrage par préfixe
  de référence via la fonction utilitaire `holidayFilterOwnedFiles()`, utilisée dans
  `holiday_prepare_head()` et `holiday/document.php`. Les portions spécifiques ATM
  sont balisées `[ATM-MDV]` en vue de leur suppression en v23+ (le stockage y devient
  propre par référence). _(branche `FIX/CONFLICT_DOCUMENTS_HOLIDAYS`)_