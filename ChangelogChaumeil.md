 Add new translation for delevrydate on supplier proposal
 Backport V23: Add hooks to modify SQL where clauses dynamically in several modules (backport https://github.com/Dolibarr/dolibarr/pull/35989)
 Backport V23: supplier order line label support (add label param to addline and clone) to preserve descriptions (backport https://github.com/Dolibarr/dolibarr/pull/36544)
 Backport V24: Add new field ref fourn on supplier proposal (backport https://github.com/Dolibarr/dolibarr/pull/36545)
 Backport V25: Option skip_update_price on FactureFournisseur::updateline() to not update the totals of a supplier invoice at each line, so that the e-invoicing import of an invoice of thousands of lines stays under the PHP time limit (backport https://github.com/Dolibarr/dolibarr/pull/43099). To remove on upgrade to V25: `git grep 'BACKPORT V25' -- htdocs/fourn/class/fournisseur.facture.class.php` lists the two places, keep the upstream code and drop the markers
