# CHANGELOG 23.0 Le-Dauphin FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

* 02/09/2026 *
- FIX : DA025138 filtre catégorie "aucune" (-1) non neutralisé sur le CA fournisseur par produit/service.
  Seule spé 14.0_ledauphin non reprise par le standard 23.0 ; alignée sur le pattern déjà
  appliqué en standard dans casoc.php et supplier_turnover_by_thirdparty.php.
  À supprimer dès que le correctif est accepté en amont sur la branche 23.0 de Dolibarr.
