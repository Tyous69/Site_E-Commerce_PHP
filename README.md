# ⌚ E-Chronos

## Plateforme de revente de montres entre particuliers

E-Chronos est une application web développée en PHP permettant aux
utilisateurs d'acheter et de vendre des montres entre particuliers à
travers une interface simple et intuitive.

Le projet a été conçu dans le cadre d'un examen de développement web et
fonctionne entièrement en **local** via un environnement **XAMPP**.

L'objectif est de proposer une expérience utilisateur fluide pour la
gestion, la mise en vente et l'achat de montres, tout en assurant un
système d'authentification sécurisé et une gestion dynamique des
annonces.

------------------------------------------------------------------------

## Fonctionnalités

-   Authentification utilisateur (inscription / connexion)
-   Gestion du compte personnel
-   Mise en vente de montres
-   Modification d'une annonce
-   Suppression d'une annonce
-   Consultation des montres disponibles
-   Ajout au panier
-   Validation d'un achat
-   Confirmation de commande
-   Gestion des montres mises en vente par l'utilisateur

------------------------------------------------------------------------

## Parcours Utilisateur

1.  L'utilisateur arrive sur la page d'accueil.
2.  Il peut consulter les montres disponibles à la vente.
3.  Il peut créer un compte ou se connecter.
4.  Une fois connecté, il peut :
    -   Mettre une montre en vente
    -   Modifier ou supprimer ses annonces
    -   Ajouter une montre au panier
5.  L'utilisateur valide son panier pour effectuer un achat.
6.  Une page de confirmation s'affiche après validation.

------------------------------------------------------------------------

## Technologies utilisées

  Technologie   Utilisation
  ------------- ----------------------------
  PHP           Backend
  MySQL         Base de données
  HTML/CSS      Interface utilisateur
  XAMPP         Serveur local Apache/MySQL
  phpMyAdmin    Gestion de la BDD

------------------------------------------------------------------------

## Structure de la Base de Données

La base de données contient les principales tables suivantes :

-   users → informations des utilisateurs
-   watches → montres mises en vente
-   cart → panier utilisateur
-   orders / transactions → validation des achats

Importée via le fichier :

    php_exam_db.sql

------------------------------------------------------------------------

## Arborescence du Projet

    E-Chronos/
    │
    ├── account.php
    ├── cart.php
    ├── confirmation.php
    ├── db.php
    ├── detail.php
    ├── edit.php
    ├── header.php
    ├── index.php
    ├── login.php
    ├── logout.php
    ├── register.php
    ├── vente.php
    ├── php_exam_db.sql
    └── README.md

------------------------------------------------------------------------

## Installation du Projet (XAMPP)

### 1. Cloner le dépôt

``` bash
git clone https://github.com/votre-repository/E-Chronos.git
```

------------------------------------------------------------------------

### 2. Placer le projet dans :

    C:\xampp\htdocs\

------------------------------------------------------------------------

### 3. Lancer XAMPP

Démarrer :

-   Apache
-   MySQL

------------------------------------------------------------------------

### 4. Importer la Base de Données

Aller sur :

    http://localhost/phpmyadmin

Créer une nouvelle base de données :

    php_exam_db

Onglet **Importer**

Sélectionner :

    php_exam_db.sql

------------------------------------------------------------------------

### 5. Vérifier la connexion BDD dans :

    db.php

------------------------------------------------------------------------

## Lancement du Projet

Dans votre navigateur :

    http://localhost/E-Chronos/

------------------------------------------------------------------------

## Contraintes

-   Le projet fonctionne uniquement en local
-   Nécessite XAMPP
-   PHP ≥ 7.4 recommandé

------------------------------------------------------------------------

## Améliorations Futures

-   Upload d'images pour les montres
-   Système de recherche
-   Filtres par marque / prix
-   Historique d'achats
-   Système de messagerie entre utilisateurs
-   Paiement en ligne
