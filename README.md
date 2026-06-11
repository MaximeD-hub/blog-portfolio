# Blog Portfolio

Application web full-stack réalisée dans le cadre du Devoir Bilan du Titre Professionnel Développeur Web et Web Mobile - Centre Européen de Formation.

## Description

Blog portfolio permettant à un développeur de publier et gérer ses articles techniques, avec un espace public pour les visiteurs et un back-office sécurisé pour l'administrateur.
Le Blog fonctionne comme un CV, chaque article représentant un projet du Développeur. Le visiteur à la possibilité de trié par catégories ici représenté par le language utilisé.
Des améliorations peuvent être ajouté, comme une barre de recherche, la possibilité d'attacher une image en créant un nouvel article, ect...

## Stack technique

- **Front-end** : HTML5, CSS3 (Vanilla), JavaScript (Vanilla)
- **Back-end** : PHP 8
- **Base de données** : MySQL
- **Outils** : XAMPP, phpMyAdmin, Git

## Prérequis

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 8)
- Un navigateur web

## Installation locale

### 1. Cloner le dépôt

```bash
git clone https://github.com/MaximeD-hub/blog-portfolio.git
```

Placer le dossier cloné dans :
C:\xampp\htdocs\

### 2. Démarrer XAMPP

- Lancer **XAMPP Control Panel**
- Démarrer **Apache** et **MySQL**

### 3. Créer la base de données

- Ouvrir [phpMyAdmin](http://localhost/phpmyadmin)
- Créer une nouvelle base de données nommée `blog_portfolio`
- Importer le fichier `database.sql` fourni à la racine du projet

### 4. Configurer la connexion

Ouvrir `includes/db.php` et vérifier les paramètres :

```php
$host   = 'localhost';
$dbname = 'blog_portfolio';
$user   = 'root';
$password = ''; // Laisser vide par défaut avec XAMPP
```

### 5. Lancer le projet

Ouvrir dans le navigateur :
http://localhost/blog-portfolio/

## Accès administrateur

- http://localhost/blog-portfolio/admin/login.php |
- Email : admin@blog.com
- Mot de passe : password

## Fonctionnalités

- Affichage des articles avec filtrage par catégorie
- Page article avec système de commentaires
- Back-office sécurisé (création, modification, suppression d'articles)
- Authentification administrateur (bcrypt)
- Protection CSRF sur les formulaires
- Design responsive (mobile & desktop)

Maxime Dubois - Formation Développeur Web & Web Mobile - CEF
