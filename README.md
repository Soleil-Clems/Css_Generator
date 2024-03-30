# CSS Generator

Ce projet, intitulé "CSS Generator", vise à comprendre le traitement des images et la gestion des fichiers en PHP. L'objectif principal est de développer un programme en ligne de commande UNIX qui concatène plusieurs images au format PNG en un seul sprite, tout en générant le fichier CSS correspondant.


## Compétences à Acquérir

Lors de ce projet, vous devrez utiliser et maîtriser les outils suivants :
- PHP
- Gestion des fichiers en PHP
- Spritesheets CSS
- Développement d'un programme en ligne de commande UNIX

## Description

Au départ, nous avons un dossier contenant plusieurs images au format PNG. L'objectif est de développer un outil en ligne de commande qui concatène ces images en un seul sprite et génère le fichier CSS correspondant à cette concaténation.



## Manuel
### Nom

**css_generator** - Générateur de sprite pour une utilisation HTML

### Synopsis

**css_generator [OPTIONS]. . . assets_folder**

### Description

Concatène toutes les images situées dans un dossier en un seul sprite et génère une feuille de style prête à l'emploi.

### Arguments Obligatoires

Les arguments obligatoires pour les options longues le sont également pour les options courtes.

- **-r, --recursive:** Recherche les images dans le dossier assets_folder passé en argument et dans tous ses sous-dossiers.
- **-i, --output-image=IMAGE:** Nom de l'image générée. Si vide, le nom par défaut est "sprite.png".
- **-s, --output-style=STYLE:** Nom de la feuille de style générée. Si vide, le nom par défaut est "style.css".

### Options Bonus

- **-p, --padding=NUMBER:** Ajoute un espacement entre les images de NUMBER pixels.
- **-o, --override-size=SIZE:** Force chaque image du sprite à avoir une taille de SIZExSIZE pixels.
- **-c, --columns_number=NUMBER:** Nombre maximum d'éléments à générer horizontalement.
