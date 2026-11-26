# CHANGELOG 23.0 Le-Dauphin FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

* 26/11/2026 *
- MERGE : DA029243 fusion de la branche communautaire origin/23.0 du 26/11/2026 (1 524 commits amont pas encore dans la branche,
  version affichée inchangée), qui apporte entre autres Dolibarr/dolibarr#41621 : un OF dont la production a
  commencé ne peut plus être remis en brouillon. Le cherry-pick de ce correctif fait juste avant la fusion est
  redondant avec elle.
- FIX : DA029243 clé ErrorMoConsumptionOrProductionStarted traduite dans htdocs/langs/fr_FR/mrp.lang
  (l'amont ne fournit que l'anglais). À supprimer quand la traduction française arrive de l'amont.

* 19/11/2026 *
- FIX : unité imprimée en libellé long (« Kilogramme ») sur le PDF d'expédition espadon, trop large pour
  la colonne : htdocs/core/modules/expedition/doc/pdf_espadon.modules.php imprime le libellé court via
  pdf_getlineunit(), comme tous les autres modèles PDF. Backport de Dolibarr/dolibarr#43192 (bloc BACKPORT),
  à supprimer une fois cette PR mergée en amont.

* 13/11/2026 *
- FIX : TK2308-5310 quantité d'un lot inférieure à 1 imprimée « 0 » sur le PDF d'expédition (et dans la
  description des lots d'une ligne de facture créée depuis une expédition) : printQty passe de %d à %s
  dans htdocs/langs/en_US et fr_FR/productbatch.lang. Les autres langues gardent %d.
  À supprimer quand printQty est en %s en amont (Dolibarr develop est toujours en %d au 13/11/2026).

* 02/09/2026 *
- FIX : DA025138 filtre catégorie "aucune" (-1) non neutralisé sur le CA fournisseur par produit/service.
  Seule spé 14.0_ledauphin non reprise par le standard 23.0 ; alignée sur le pattern déjà
  appliqué en standard dans casoc.php et supplier_turnover_by_thirdparty.php.
  À supprimer dès que le correctif est accepté en amont sur la branche 23.0 de Dolibarr.
