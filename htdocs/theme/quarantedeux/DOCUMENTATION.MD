# Documentation Technique

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants :

-   Node.js et npm installés sur votre système
-   Sass installé sur votre système

### Installer Sass sur Mac

Si vous êtes sur Mac et que vous n'avez pas Sass d'installer, vous pouvez le faire en utilisant Homebrew :

1.  Ouvrez un terminal
    
2.  Tapez la commande suivante pour installer Sass en utilisant npm :

    `npm install -g sass`
    
    Si la commande renvoie une erreur de permission, vous pouvez utiliser la commande `sudo` pour exécuter l'installation avec des privilèges administratifs :
    
    `sudo npm install -g sass`

## Compiler un thème Dolibarr avec Sass

### Compiler un thème Dolibarr avec Sass sur un terminal

1.  Ouvrez un terminal
    
2.  Naviguez jusqu'au dossier de votre thème Dolibarr
    
3.  Tapez la commande suivante pour compiler votre fichier Sass :
  
    
    `sass --no-source-map --watch scss/style.scss:scss.css.php` 
    
    Cette commande surveillera les modifications apportées à votre fichier style.scss et les compilera automatiquement en css dans le fichier scss.css.php. Si vous souhaitez arrêter la surveillance, appuyez sur `Ctrl + C`.
    

### Compiler un thème Dolibarr avec Sass en utilisant Visual Studio Code

Si vous utilisez Visual Studio Code, vous pouvez compiler votre thème Dolibarr en utilisant la tâche Sass préconfigurée.

1.  Ouvrez votre dossier de thème Dolibarr dans Visual Studio Code
2.  Accédez à l'onglet "Terminal", puis "Run Task"
3.  Sélectionnez la tâche "SASS Complie"
4.  Appuyez sur `Entrée` pour compiler votre thème Dolibarr avec Sass.

Cela devrait compiler votre thème Dolibarr en utilisant Sass et générer le fichier scss.css.php à partir du fichier style.scss.