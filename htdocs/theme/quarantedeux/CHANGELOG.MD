# CHANGELOG THEME CODE42 FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

Toutes les modifications notables apportées à ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
et ce projet adhère au [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

# <span style='color:white;background-color:#ed6b00;border-radius:5px;padding: 5px;font-size:small'>Nouveautés</span> # [18.0.01] - 2024-??-??

- Changed :
  - [#213] La couleur des badges dans les onglets ont la meme couleur que le menu principal
  
# [18.0.00] - 2023-12-21

- Added :
  - [#197] Nouvelle fonctionnalité pour l'affichage des scrollbars (pour l'activer, cocher la case "Afficher les bordures G-D..." dans le menu Affichage/Thème)

- Fixed :
  - [#155] Optimisations du menu principal et du breadcrumb
  - [#194] Ajustement visuel de la barre d'action
  - [#199] Compatiblité avec le dark mode
  - [#207] Prise en charge des personnalisation du menu principal (icônes + textes...)
  - [#209] La barre d'action se place le plus possible en bas de l'écran
  - [#238] Le Scroll se repositionne correctement
  - [#247] Le Scroll est posible dans les fiches ticket publique
  - [#254] Le datepicker dans le modale "makeorder" se positionne correctement
  - [#253] L'option pour retracter le menu apparait correctement

# [13.6.01] - 2023-12-18

- Added :
  - [#230] Ajout d'un script pour réduire le menu secondaire

- Fixed :
  - [#229] Le bouton impression est affiché à nouveau

# [13.6.00] - 2023-11-24

- Added :
  - [#195] Le thème a dorénavant un module
  - [#217] Ajout d'un onglet Changelog
  - [#211] Ajout de ScriptInject
  - [#214] Ajout de HideTopMenu
  - [#221] La CI à été rajouter

- Changed :
  - [#220] Modification du readme pour integrer celui de ScriptsInject & HideTopMenu

# [13.5.10] - 2023-10-31

- Fixed :
  - [#215] Le datepicker ne passe plus en dessous lors du clonage d'un objet
  - [#219] Le popup pour telecharger directement les documents généré est réafficher correctement
  - [#203] Optimisation d'une cardd au niveau des tableaux et reduction de la marge gauche et droite
  - [#204] Le bouton "Vider" dans la liste des modules à une bordure et le style du thème
  - [#201] Le Listing des colonnes à afficher est revenu
  - [#198] Le bandeau au niveau des tickets ne passe plus par dessus

# [13.5.00] - 2023-10-31

- Added :

  - [#193] Script pour passer une commande avec un pop ajouté

- Fixed :
  - [#201] La liste des colonnes n'est plus masqué sur le listing
  - [#198] Le bordereaux ne passe plus sur le listing
  - [#204] Le bouton "vider" possède un style
  - [#190] Le menu burger est réutilisable en vue paysage
  - [#192] Les boutons sont réalligner
  - [#188] Les colorPicker ne sont plus cassé
  - [#191] PackingList est compatible avec le thème

# [13.4.00] - 2023-07-21

- Added :

  - [#129] Ajout d'une documentation technique

- Changed :

  - [#180] TabsAction est maintenant de la meme couleur que le menu secondaire
  - [#174] TabsAction est maintenant le + en avant possible

- Fixed :
  - [#187] l'écart de tabsAction sur une card est corriger
  - [#172] le bouton enregistrer dans la partie "enregistrer une reception" à maintenant la bonne couleur
  - [#186] les inputs date ne sont plus cassé dans le listing des fournisseurs
  - [#175] la plupart des zindex defaillant sont corriger
  - [#183] le thème est comptabile quicklist
  - [#182] l'icone "fav" d'un article est de nouveau jaune
  - [#179] la fiche de pointage n'est plus cassée en vue mobile
  - [#177] les icones dans les boutons sont bien placée
  - [#176] le thème est compatible avec recherche avancée
  - [#170] le Fil d'ariane refonctionne sur mobile
  - [#139] Le Repositionnement est de nouveau actif
  - [#169] Le Bloc User du Top menu est de nouveau accesible en mobile
  - [#162] Les items du kanban ne passe plus devant le menu (mobile)
  - [#130] Me menu ne gêne plus l'onglet "mouvement de stock"
  - [#166] Mise en avant du bouton éditer
  - [#161] Le menu active est plus lisible au format mobile
  - [#150] Bouton de couleur vert lors de la validation de l'intervention
  - [#149] Icônes non centré présent sur le menu déroulant
  - [#147] Reduction de la taille des filtres par tags en format téléphone
  - [#145] Historique des interventions responsive

# [13.1.02] - 2023-03-28

- Added :

  - [#138] Ajout d'une vignette pour le thème

- Fixed :
  - [#168] La fancybox dans une facture n'apparait plus
  - [#165] La fenetre d'envoi d'email est corrigé
  - [#159] Les boutons sont de nouveau centré
  - [#158] Les boites ne sont plus tronqué
  - [#160] Les listes sont scrollable sur téléphone
  - [#167] Le selecteur de modèle d'email réapparait correctement
  - [#156] Le bouton 'passer les commandes' est bien placer
  - [#135] la barre de navigation n'est plus raccourcie
  - [#140] Fix des boutons et de MyBooking dans la tabs action
  - [#117] Les drapeaux ont diminuer de taille
  - [#120] Fix des boutons pour dol16
  - [#125] Les boutons ont retrouvé une taille normale + ils se mettent à la ligne lorsqu'ils n'ont plus de place
  - [#133] Le zoom est coordonné avec le menu
  - [#131] Les icones des modules sont de nouveau visible
  - [#108] Les items dans la navbar des receptions sont espacés
  - [#164] La barre des pièces est maintenant scrollable
  - [#157] Le Changelog à été mis à jour
  - [#152] Le menu secondaire est accesible
  - [#141] Le menu "plus" est accesible
  - [#123] Les icones dans les boutons sont centrer
  - [#115] Le Décalage au niveau des filtres des dates est maintenant résolu

# [13.1.01] - 2023-02-15

- Fixed :

  - [#107] Bordures et Arrondis moins prononcé
  - [#106] Correction des zones de recherches dans le listing d'intervention
  - [#101] Onglet Configuration
  - [#100] Onglet du Topmenu réapparu
  - [#99] Interface onglet Contact corrigé
  - [#98] Icones du menu Cotrolia centré
  - [#97] Zone de texte "WYSIWYG" sans bordure
  - [#93] Zoom Général dimunier à 85%

- Changed :
  - [#109] Modification du comportement du thème sur l'historique d'une intervention
  - [#94] Modification des couleurs de boutons
  - [#92] Modification des réceptions
  - [#91] Modification du tableau de bord

# [13.1.00] - 2023-02-14

- Added :

  - [#89] Ajout de bordure sur le fil d'Ariane

- Changed :
  - [#95] Application d'une bordure aux boutons
  - [#88] Modification de l'affichage des interventions
  - [#87] Modification du listing d'interventions
  - [#86] Modification du comportement du menu
  - [#90] Préparation des Fichiers
