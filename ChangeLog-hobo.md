# ChangeLog ATM — fork hobo (`atm/20.0_hobo`)

Suivi des correctifs et évolutions **spécifiques ATM** appliqués au fork hobo, en
complément du `ChangeLog` Dolibarr upstream (qui n'est pas modifié).

## 20.0.4 — hobo

### FIX
- **Holiday** : les pièces jointes et le badge de comptage d'une demande de congés
  affichaient aussi les fichiers d'autres demandes partageant le même dossier de
  stockage (`get_exdir()` niveau 2 basé sur l'id). Ajout d'un filtrage par préfixe
  de référence via la fonction utilitaire `holidayFilterOwnedFiles()`, utilisée dans
  `holiday_prepare_head()` et `holiday/document.php`. Les portions spécifiques ATM
  sont balisées `[ATM-MDV]` en vue de leur suppression en v23+ (le stockage y devient
  propre par référence). _(branche `FIX/CONFLICT_DOCUMENTS_HOLIDAYS`)_