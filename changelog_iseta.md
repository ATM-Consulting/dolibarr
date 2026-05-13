# ChangeLog des ajouts spécifiques client

## 22.0_iseta
- FIX : ticket DA022737 - Retrait du spérateur des milliers pour l'export Charlemagne - **20/02/2023** - 14.0_iseta
- FIX : Modification de l'export Charlemagne pour inclure le code analytique et les dates (vides) à la fin - **06/07/2022** - 14.0_iseta
- **14/04/2021** L’affichage des lignes de facture et leur enregistrement dans le grand livre sont modifiés :
  pour les débits et les crédits, la colonne "Libellé du compte" contient désormais toujours le nom du tiers ;
  pour les débits, la colonne "Libellé opération" contient désormais uniquement la réf. du service de la ligne de
  facture correspondante et pour les crédits, la réf. du service précédée du nom du tiers.
  2b62296b505b64c8c155ecdf65ae13e31de1494a
