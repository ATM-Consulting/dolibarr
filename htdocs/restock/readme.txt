ATTENTION : il est n�cessaire d'effectuer un correctif dans dolibarr (il sera pr�sent dans la prochaine version 3.5.2).

L'erreur � corriger se trouve dans le fichier /htdocs/fourn/class/fournisseur.class.php

Dans la fonction  function ListArray() ligne 180
--> ajouter au d�but "global $user;" 
--> remplacer toutes les occurences de "$this->user->" par "$user"


1.0.3 : hearder forwarding correct issue