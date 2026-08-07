# CliniClic — gestion de clinique avec Symfony

![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![Symfony 7.4 LTS](https://img.shields.io/badge/Symfony-7.4%20LTS-000000?logo=symfony)
![Doctrine ORM 3.6](https://img.shields.io/badge/Doctrine%20ORM-3.6-FC6A31?logo=doctrine&logoColor=white)
![Bootstrap 5](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)

CliniClic est une application académique de gestion de clinique modernisée pour démontrer une architecture Symfony claire, des règles métier testables manuellement et une protection stricte des données médicales. Elle conserve les modules CRUD historiques du projet et ajoute une gestion professionnelle des rôles, des rendez-vous, des disponibilités, des consultations et des prescriptions.

> Toutes les données et tous les comptes documentés ici sont fictifs. Cette application est une démonstration de portfolio, pas un dispositif médical ni un logiciel prêt pour héberger de vraies données de santé.

## Aperçu

| Accueil public | Tableau de bord administrateur |
|---|---|
| ![Accueil public CliniClic](docs/screenshots/home.png) | ![Tableau de bord administrateur](docs/screenshots/admin-dashboard.png) |

L’interface publique et l’espace authentifié sont également adaptés aux écrans mobiles : [voir la capture mobile](docs/screenshots/home-mobile.png).

## Fonctionnalités

- authentification Symfony, inscription patient, déconnexion POST protégée par CSRF et limitation des tentatives de connexion ;
- quatre rôles : administrateur, médecin, réceptionniste et patient ;
- tableaux de bord alimentés par les données Doctrine et adaptés au rôle connecté ;
- gestion des utilisateurs par l’administrateur et édition sécurisée du profil ;
- rendez-vous filtrés, triés et paginés avec statuts contrôlés ;
- disponibilités hebdomadaires des médecins ;
- prévention des rendez-vous passés, des chevauchements médecin/patient et des créneaux hors disponibilité ;
- consultations et prescriptions privées, accessibles uniquement au patient concerné et au médecin assigné ;
- gestion du stock médical, des catégories et des alertes de quantité ;
- événements, capacité, participation unique et images validées côté serveur ;
- réclamations administratives, réponses et export PDF ;
- pages 403, 404 et 500 cohérentes avec l’identité visuelle ;
- interface responsive et accessible basée sur Bootstrap 5 et des ressources locales.

## Rôles et autorisations

| Action | Admin | Médecin | Réception | Patient |
|---|:---:|:---:|:---:|:---:|
| Gérer les utilisateurs et référentiels | Oui | Non | Non | Non |
| Voir les rendez-vous autorisés | Tous | Assignés | Tous | Les siens |
| Créer ou gérer une demande de rendez-vous | Oui | Limité | Oui | Les siens |
| Gérer les disponibilités | Toutes | Les siennes | Lecture | Non |
| Créer une consultation/prescription | Non | Si assigné | Non | Non |
| Lire une consultation/prescription | Non | Si assigné | Non | La sienne |
| Gérer stock et événements | Oui | Non | Selon l’écran | Non |
| Éditer son profil | Oui | Oui | Oui | Oui |

Les restrictions sont appliquées dans `security.yaml`, dans les contrôleurs et par des voters. Masquer un bouton Twig n’est jamais considéré comme une autorisation suffisante.

## Stack technique

- PHP 8.2 minimum ; PHP 8.4 recommandé pour un nouvel environnement ;
- Symfony 7.4 LTS ;
- Doctrine ORM 3.6.7 et Doctrine DBAL 4.4 ;
- Twig 3.28 et Symfony Forms/Validator/Security 7.4 ;
- MariaDB 10.6+ ou MySQL compatible ;
- Bootstrap 5, Bootstrap Icons et CSS applicatif local ;
- KnpPaginatorBundle 6.10 ;
- Dompdf 3.1 pour les exports existants.

Doctrine ORM est volontairement limité à l’intervalle `>=3.6.7 <3.6.8` : la version `3.6.8` disponible pendant la modernisation utilisait une API attendue de DBAL 4.5 alors que DBAL stable était encore en 4.4. Cette contrainte évite une combinaison instable tout en restant sur une version maintenue.

## Architecture

Le projet conserve une architecture Symfony classique et facile à expliquer :

```text
Requête HTTP
   └── Controller          reçoit la requête et vérifie l’accès
         ├── Voter         contrôle le rôle, l’assignation ou la propriété
         ├── Form          transforme et valide les données utilisateur
         ├── Service       applique les règles métier et la transaction
         └── Repository    exécute les requêtes Doctrine réutilisables
               └── Entity  porte le modèle et les contraintes de données
```

Les services principaux sont :

- `AppointmentService` : disponibilités, conflits, verrouillage pessimiste et transitions de statut ;
- `DoctorAvailabilityService` : cohérence des plages horaires ;
- `ConsultationService` : création du dossier médical et finalisation du rendez-vous ;
- `DashboardService` : indicateurs réels et limités au rôle ;
- `EventParticipationService` : capacité et participation unique sous transaction ;
- `EventImageUploader` : validation et nommage sûr des images.

Une vue détaillée des relations, du workflow et des responsabilités est disponible dans [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md). L’état initial avant correction se trouve dans [docs/AUDIT_INITIAL.md](docs/AUDIT_INITIAL.md).

## Prérequis

- PHP `>= 8.2` avec `ctype`, `iconv`, `pdo_mysql`, `dom`, `json`, `tokenizer` et `zip` ; l’extension `intl` est recommandée ;
- Composer 2 ;
- MariaDB 10.6+ ou MySQL compatible ;
- Git, uniquement pour cloner le dépôt.

Avec XAMPP sous Windows, vérifiez que `extension=zip` et `extension=pdo_mysql` sont activées dans `php.ini`. La modernisation a été validée localement avec PHP 8.2.12 et MySQL/MariaDB sur le port configuré dans `.env.local`.

## Installation

```bash
git clone <URL_DU_DEPOT> cliniclic
cd cliniclic
composer install
```

Créer la configuration locale sans modifier le modèle versionné :

```powershell
Copy-Item .env.example .env.local
```

Sous Linux ou macOS :

```bash
cp .env.example .env.local
```

Dans `.env.local`, remplacez au minimum `APP_SECRET` et `DATABASE_URL` :

```dotenv
APP_ENV=dev
APP_DEBUG=1
APP_SECRET=une_valeur_locale_longue_et_aleatoire
APP_TIMEZONE=UTC
DATABASE_URL="mysql://clinic_user:mot_de_passe@127.0.0.1:3306/cliniclic?serverVersion=10.6.0-MariaDB&charset=utf8mb4"
MAILER_DSN=null://null
MAILER_FROM=no-reply@cliniclic.local
```

Créer la base et appliquer toutes les migrations :

```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
```

Charger uniquement les comptes et données de démonstration fictifs :

```bash
php bin/console app:demo:users
```

La commande est réexécutable : elle met à jour les comptes de démonstration sans créer de doublons.

## Comptes fictifs

Mot de passe commun en environnement de démonstration : `ClinicDemo!2026`

| Rôle | Adresse |
|---|---|
| Administrateur | `admin@cliniclic.test` |
| Médecin | `doctor@cliniclic.test` |
| Réceptionniste | `reception@cliniclic.test` |
| Patient | `patient@cliniclic.test` |

N’utilisez jamais ces identifiants dans un environnement réel. La commande `php bin/console app:user:create` permet de créer manuellement un utilisateur avec un mot de passe distinct.

## Exécution locale

Avec Symfony CLI :

```bash
symfony server:start
```

Avec le serveur PHP intégré :

```bash
php -S 127.0.0.1:8000 -t public public/router.php
```

Sous XAMPP/PowerShell si PHP n’est pas dans le `PATH` :

```powershell
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8000 -t public public/router.php
```

Ouvrir ensuite <http://127.0.0.1:8000/home>. Le fichier `public/router.php` permet au serveur intégré de servir directement les CSS, scripts, images et polices avant de déléguer les routes applicatives à Symfony.

`APP_TIMEZONE` vaut `UTC` par défaut afin que le comportement ne dépende pas de la configuration PHP de la machine. Définissez le fuseau IANA de la clinique, par exemple `Africa/Tunis`, avant de créer des rendez-vous réels.

## Routes principales

| Espace | Route |
|---|---|
| Accueil public | `/home` |
| Connexion / inscription | `/login`, `/register` |
| Tableau de bord par rôle | `/` |
| Rendez-vous | `/rendez-vous/` |
| Disponibilités | `/doctor/availability` |
| Consultations privées | `/medical/consultations/` |
| Événements publics | `/evenement` |
| Réclamation publique | `/addReclamation` |
| Gestion des utilisateurs | `/admin/users` |
| Stock | `/stock` |

## Règles du workflow de rendez-vous

- un créneau doit être futur et couvert par une disponibilité active du médecin ;
- un médecin et un patient ne peuvent pas avoir deux rendez-vous qui se chevauchent ;
- une transaction et des verrous pessimistes limitent les réservations concurrentes ;
- statuts autorisés : `pending`, `confirmed`, `completed`, `cancelled`, `no_show` ;
- une annulation libère immédiatement le créneau ;
- seul le médecin assigné peut créer la consultation qui termine le rendez-vous ;
- un patient ne voit que ses rendez-vous et son dossier médical ;
- l’administrateur et la réception ne voient pas les notes cliniques.

## Sécurité et confidentialité

- mots de passe gérés par le hasher Symfony ;
- CSRF sur les formulaires et toutes les mutations, y compris la déconnexion ;
- jeton CSRF d’API récupérable par un administrateur sur `/API/csrf-token` puis envoyé dans `X-CSRF-Token` ;
- limitation de connexion à cinq tentatives par fenêtre de quinze minutes ;
- contrôle d’accès par rôle et voters de propriété ;
- en-têtes `nosniff`, anti-framing, politique de référent et permissions réduites ;
- réponses médicales et rendez-vous marqués `private, no-store` ;
- aucune donnée médicale dans les tableaux de bord administratifs ;
- requêtes Doctrine paramétrées ;
- échappement Twig conservé, sans utilisation de `|raw` ;
- upload d’images limité à 2 Mo, aux MIME JPG/PNG/WebP et à des noms générés ;
- secrets locaux placés dans `.env.local`, ignoré par Git.

Pour la production, utilisez `APP_ENV=prod`, `APP_DEBUG=0`, HTTPS, un secret fort, un compte SQL à privilèges limités et un transport e-mail réellement protégé. Ne stockez jamais de vrais documents médicaux dans `public/`.

## Vérifications de maintenance

```bash
php bin/console lint:container
php bin/console lint:twig templates
php bin/console lint:yaml config
php bin/console doctrine:schema:validate
php bin/console doctrine:migrations:up-to-date
composer validate --strict
composer audit
```

Les tests automatisés, CI/CD, Docker, déploiement et monitoring ne sont volontairement pas ajoutés, conformément au périmètre académique demandé.

## Structure utile

```text
src/Controller/          contrôleurs HTTP minces
src/Entity/              entités Doctrine
src/Repository/          recherche, filtres et statistiques
src/Service/             règles métier réutilisables
src/Security/Voter/      autorisations dépendantes des objets
src/Form/                formulaires Symfony
src/Enum/                statuts contrôlés
templates/               vues Twig publiques et authentifiées
public/assets/css/       design system local
migrations/              évolution sûre du schéma
docs/                    audit, architecture, journal et captures
```

## Limites connues

- pas de vérification d’adresse e-mail ni de récupération de mot de passe ;
- pas de module de documents médicaux privés ;
- spécialités, départements, facturation et paiements ne faisaient pas partie du projet existant ;
- les profils métier restent portés par `User` et ses rôles, sans entités détaillées médecin/patient ;
- l’ancien modèle conserve temporairement quelques colonnes de rendez-vous pour faciliter la migration des données historiques ;
- l’envoi réel des e-mails est désactivé par défaut.

## Évolutions possibles

1. ajouter la vérification e-mail et la réinitialisation sécurisée du mot de passe ;
2. séparer les profils patient et médecin lorsque davantage d’attributs métier seront nécessaires ;
3. proposer des documents médicaux chiffrés stockés hors du répertoire public ;
4. ajouter spécialités et départements avant d’enrichir la recherche de médecins ;
5. compléter l’historique d’activité avec une politique de conservation explicite.

Le détail des corrections, migrations et fichiers concernés est disponible dans [docs/IMPLEMENTATION_LOG.md](docs/IMPLEMENTATION_LOG.md).
