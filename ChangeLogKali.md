ChangeLogKali 

##BACKPORT
(PR#N°PR - Intitulé) 
(A supprimer de la liste lors de la prochaine MDV) 
PR#32662 - Allow global search on services list
PR#40082 - Adding Extrafields on invoice rec line creation
PR#40049 - Adding conf MAIN_SHOW_TECHNICAL_ID on contractline list
PR#40041 - NB_REC_FACT_CUSTOMER_GEN_BY_CALL
PR#39062 - Add possibility to add contact on API ticket creation
PR#41643 - Shipment and reception cards show one line per lot/serial with a show/hide toggle, like the product stock tab (T260251)
PR#43074 - Sanitize ticket messages on the ticket public interface (T260272)
PR#43195 - Quadratic update_price when generating a supplier invoice from a template (T250214)
PR#43233 - A link extrafield left empty was stored as 0 instead of NULL (T250214)
PR#43290 - A chkbxlst extrafield filtered on $ID$ broke the search input of list views (T250214)
PR#43306 - A link extrafield to a category showed a bad setup error instead of its combo (T250214)
PR#41087 - Hook printFieldListSearchParam on the reception list (TK2609-5362)


## SPÉ KOESIO 
(N°Commit - Intitulé)
fb00fbd0b5e - fire TICKET_CREATE so the creation notification reaches the ticket contacts
cbdaaea84ab - Suppression de l'envoi automatique au tiers du ticket
fbde090e43db98684e83d96bc8db51fcb0904949 - Limite mémoire relevée à 2048 Mo
2897cb7ad87a63089b48ad5298031475ad48213d - TVA du vendeur et non du client sur un contrat fournisseur
a1428b79cf0 - Génération anticipée des factures récurrentes
369607840b9 - Hide edit button when date_compta is not empty
060f6d221fe - insertExtraFields ne vide plus un extrafield link dont l'id est introuvable (fk_last_invoice) (T260197)
b200f586161 - Bouton Modifier de la facture fournisseur masqué si date_compta est non vide (et plus seulement non définie)
b200f586161 - Bloc « Lier à » masqué sur la facture fournisseur
b200f586161 - Lignes jamais pré-cochées dans la liste des services de contrat
b200f586161 - Date de création affichée sur la fiche lot/série
b200f586161 - Contrôle du code client désactivé dans Societe::verify()
