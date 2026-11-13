# CHANGELOG 23.0 Le-Dauphin FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

* 13/11/2026 *
- FIX : T7037 quantité d'un lot inférieure à 1 imprimée « 0 » sur le PDF d'expédition (et dans la
  description des lots d'une ligne de facture créée depuis une expédition) : printQty passe de %d à %s
  dans htdocs/langs/en_US et fr_FR/productbatch.lang. Les autres langues gardent %d.
  À supprimer quand printQty est en %s en amont (Dolibarr develop est toujours en %d au 13/11/2026).

* 02/09/2026 *
- FIX : DA025138 filtre catégorie "aucune" (-1) non neutralisé sur le CA fournisseur par produit/service.
  Seule spé 14.0_ledauphin non reprise par le standard 23.0 ; alignée sur le pattern déjà
  appliqué en standard dans casoc.php et supplier_turnover_by_thirdparty.php.
  À supprimer dès que le correctif est accepté en amont sur la branche 23.0 de Dolibarr.
