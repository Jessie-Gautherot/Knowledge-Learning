# Knowledge Learning

## Présentation

Knowledge Learning est une application Symfony réalisée dans le cadre d’un TP.

Il s’agit d’une plateforme de formation en ligne permettant aux utilisateurs de choisir un thème de formation et d’acheter des cursus complets ou des leçons individuelles afin de se former à distance.

L’application utilise une base de données MySQL, des fixtures pour générer des données de démonstration ainsi qu’un paiement simulé via Stripe en mode test (Sandbox).

## 1. Prérequis

Avant de commencer, assurez-vous d’avoir installé :

- PHP 8.2.12 ou supérieur
- Symfony Framework 7.4
- Symfony CLI (v5.16.1)
- Composer 2.9.5
- MySQL Community Server 8.0.46
- Un navigateur web
- Un terminal compatible bash

## 2. Cloner le projet

```bash
git clone https://github.com/Jessie-Gautherot/Knowledge-Learning.git
cd Knowledge-Learning
```

## 3. Configuration des environnements

Le projet utilise deux environnements :

- un environnement de développement : `.env.local`
- un environnement de test : `.env.test.local`

Chaque environnement utilise sa propre base de données.

1. À l'aide des fichiers `.env.example` et `.env.test.example`, créer respectivement les fichiers `.env.local` et `.env.test.local`.

2. Adapter la valeur de `DATABASE_URL` selon votre configuration MySQL locale.

3. Ajouter dans le fichier `.env.local` les valeurs des variables `MAILER_DSN`, `STRIPE_SECRET_KEY` et `STRIPE_PUBLISHABLE_KEY` fournies dans le rendu du devoir.

## 4. Installation du projet

### 4.1 Installer les dépendances Composer

```bash
composer install
```

### 4.2 Créer la base de données de développement

```bash
php bin/console doctrine:database:create
```

### 4.3 Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate
```

Répondre `yes` si Symfony demande une confirmation.

### 4.4 Charger les fixtures

```bash
php bin/console doctrine:fixtures:load
```

Répondre `yes` si Symfony demande une confirmation.

Les fixtures créent automatiquement les utilisateurs ainsi que les données de démonstration nécessaires au fonctionnement de l'application.

## 5. Lancer le projet

Démarrer le serveur Symfony :

```bash
symfony server:start
```

L'application est accessible à l'adresse :

```text
http://127.0.0.1:8000
```

Pour arrêter le serveur :

```bash
symfony server:stop
```

## 6. Prise en main de l'application

### 6.1 Inscription et activation de votre compte

Créez un compte utilisateur via le formulaire d'inscription.

Après l'inscription, un e-mail d'activation est envoyé à l'adresse renseignée.

> **Attention :**
> Selon le fournisseur de messagerie utilisé, l'e-mail d'activation peut être automatiquement classé dans les courriers indésirables (Spam). En cas de non-réception, merci de vérifier les dossiers Spam, Promotions et Notifications.

Le compte doit être activé à l'aide du lien contenu dans cet e-mail, avant de pouvoir acheter une leçon ou un cursus.

Si votre compte n'est pas activé, vous pourrez vous connecter, mais vous ne pourrez pas acheter de leçon ou de cursus.

### 6.2 Comptes de démonstration

Les comptes suivants sont créés automatiquement lors du chargement des fixtures.

#### Compte Administrateur

- Email : `admin@test.com`
- Mot de passe : `AdminPass1`

Connectez-vous avec ce compte pour accéder au backoffice et consulter la gestion des utilisateurs, des contenus et des achats.

#### Compte Client

- Email : `client@test.com`
- Mot de passe : `Password1`

### 6.3 Paiement Stripe (mode test)

- Aucun compte Stripe n’est nécessaire.
- Les paiements sont simulés en mode test (Sandbox).

### 6.4 Effectuer un achat de test

Utiliser la carte de test Stripe suivante :

- Email : au choix
- Numéro : `4242 4242 4242 4242`
- Date d’expiration : n’importe quelle date future
- CVC : 3 chiffres au choix
- Nom du titulaire de la carte : au choix

### 6.5 Validation des cursus et certifications

- Pour valider un cursus, veuillez valider l'ensemble des leçons qui le composent.
- Lorsqu'un thème est entièrement validé (c'est-à-dire lorsque tous ses cursus sont validés), la certification associée devient disponible dans la page des certifications.

## 7. Lancer les tests

Les tests utilisent l'environnement `test`.

### 7.1 Créer la base de données de test

```bash
php bin/console doctrine:database:create --env=test
```

### 7.2 Exécuter les migrations sur la base de test

```bash
php bin/console doctrine:migrations:migrate --env=test
```

Répondre `yes` si Symfony demande une confirmation.

### 7.3 Charger les fixtures dans la base de test

```bash
php bin/console doctrine:fixtures:load --env=test
```

Répondre `yes` si Symfony demande une confirmation.

### 7.4 Exécuter tous les tests PHPUnit

```bash
php bin/phpunit
```