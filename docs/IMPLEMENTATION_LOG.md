# Journal de modernisation

Ce journal complète l’[audit initial](AUDIT_INITIAL.md). Il regroupe les changements par phase, leur justification et les opérations de base de données nécessaires.

## Phase 1 — Stabilisation et sécurité immédiate

### Changements

- correction des appels Doctrine erronés qui provoquaient plusieurs erreurs 500 ;
- remplacement des suppressions GET par des formulaires POST avec jeton CSRF ;
- suppression des rendus Twig `|raw` non justifiés ;
- suppression des identifiants utilisateur et adresses e-mail codés en dur ;
- validation des uploads d’image et génération de noms sûrs ;
- pages d’erreur 403, 404 et 500 ;
- centralisation des alertes Twig et du thème Bootstrap des formulaires ;
- ajout de `.env.example` et durcissement de `.gitignore` ;
- retrait du contrôleur e-mail inutilisé et des templates morts identifiés.

### Fichiers principaux

- `src/Controller/EvenementController.php`
- `src/Controller/EvenementAPIController.php`
- `src/Controller/MailerController.php`
- `src/Service/EventImageUploader.php`
- `templates/bundles/TwigBundle/Exception/*`
- `templates/components/_flash.html.twig`
- `.env.example`, `.gitignore`

## Phase 2 — Authentification et contrôle d’accès

### Changements

- formulaire de connexion Symfony et inscription patient ;
- rôles constants `ROLE_ADMIN`, `ROLE_DOCTOR`, `ROLE_RECEPTIONIST`, `ROLE_PATIENT` ;
- gestion administrative des utilisateurs et profil personnel ;
- mot de passe de douze caractères minimum et hasher Symfony ;
- déconnexion POST avec CSRF ;
- jeton CSRF dédié dans l’en-tête `X-CSRF-Token` pour les mutations de l’API événement ;
- limitation à cinq tentatives de connexion par quinze minutes ;
- en-têtes de sécurité et cache privé pour les données sensibles ;
- commandes de création d’utilisateur et de données fictives.

### Fichiers principaux

- `config/packages/security.yaml`
- `config/packages/framework.yaml`
- `src/Entity/User.php`
- `src/Controller/SecurityController.php`
- `src/Controller/AdminUserController.php`
- `src/Controller/ProfileController.php`
- `src/Security/UserChecker.php`
- `src/EventSubscriber/SecurityHeadersSubscriber.php`
- `src/Command/CreateUserCommand.php`
- `src/Command/LoadDemoUsersCommand.php`
- `templates/security/*`, `templates/registration/*`, `templates/admin/user/*`, `templates/profile/*`

## Phase 3 — Rendez-vous et disponibilités

### Changements

- enum `AppointmentStatus` ;
- relation explicite du rendez-vous vers le patient et le médecin ;
- horaires, durée, notes et horodatages ;
- disponibilités hebdomadaires ;
- service de réservation avec transaction, verrous pessimistes et détection des chevauchements ;
- transitions de statut contrôlées ;
- voter de propriété ;
- recherche, filtres et pagination ;
- tableau de bord réel selon le rôle.

### Fichiers principaux

- `src/Entity/RDV.php`, `src/Entity/DoctorAvailability.php`
- `src/Enum/AppointmentStatus.php`
- `src/Service/AppointmentService.php`
- `src/Service/DoctorAvailabilityService.php`
- `src/Service/DashboardService.php`
- `src/Security/Voter/AppointmentVoter.php`
- `src/Repository/RDVRepository.php`
- `src/Controller/RDVController.php`
- `src/Controller/DoctorAvailabilityController.php`
- `templates/rdv/*`, `templates/doctor_availability/*`, `templates/index/index.html.twig`

## Phase 4 — Dossier médical minimal et privé

### Changements

- entités consultation et prescription ;
- consultation unique par rendez-vous ;
- accès patient/médecin assigné via voter ;
- création possible uniquement après le début d’un rendez-vous confirmé ;
- passage automatique du rendez-vous à `completed` ;
- aucune suppression fonctionnelle afin de préserver l’historique ;
- exclusion explicite des administrateurs et réceptionnistes.

### Fichiers principaux

- `src/Entity/Consultation.php`, `src/Entity/Prescription.php`
- `src/Repository/ConsultationRepository.php`, `src/Repository/PrescriptionRepository.php`
- `src/Service/ConsultationService.php`
- `src/Security/Voter/ConsultationVoter.php`
- `src/Controller/ConsultationController.php`
- `src/Form/ConsultationType.php`, `src/Form/PrescriptionType.php`
- `templates/consultation/*`

## Phase 5 — Doctrine, concurrence et qualité du code

### Changements

- contraintes uniques sur les catégories et les participations ;
- index de recherche des créneaux ;
- service transactionnel pour la capacité des événements ;
- refus de supprimer un événement encore lié à des participants ;
- longueurs de colonnes alignées sur les validations ;
- champs de réclamation cohérents et non nuls ;
- types Doctrine alignés avec DBAL 4 ;
- suppression de la table Messenger vide et inutilisée ;
- relations renommées en camelCase et retrait des cascades risquées ;
- suppression des squelettes de repository commentés.

### Fichiers principaux

- `src/Service/EventParticipationService.php`
- `src/Exception/EventParticipationException.php`
- `src/Entity/Reclamation.php`, `src/Entity/Reponse.php`
- `src/Repository/*`
- `migrations/Version20260806210000.php`
- `migrations/Version20260806211500.php`
- `migrations/Version20260806223000.php`
- `migrations/Version20260806223100.php`
- `migrations/Version20260806233000.php`
- `migrations/Version20260806234000.php`

## Phase 6 — Symfony 7.4 et dépendances

### Changements

- passage de Symfony 6.1 non maintenu à Symfony 7.4 LTS ;
- Doctrine ORM 2.14 vers 3.6.7 et DBAL 4.4 ;
- Twig 3.28, Dompdf 3.1 et KnpPaginator 6.10 ;
- routes migrées vers `Symfony\Component\Routing\Attribute\Route` ;
- résolution Doctrine des paramètres rendue explicite avec `MapEntity`, sans auto-mapping déprécié ;
- remplacement de l’ancienne méthode `renderForm()` ;
- suppression de SensioFrameworkExtraBundle, Doctrine Annotations, VichUploader, Messenger, Notifier et autres dépendances non utilisées ;
- ajout de RateLimiter pour la connexion ;
- aucun avis de vulnérabilité signalé par `composer audit` lors de la livraison.

`doctrine/orm` est limité à `>=3.6.7 <3.6.8` pour éviter une incompatibilité constatée entre ORM 3.6.8 et DBAL stable 4.4 au moment de la mise à jour.

## Phase 7 — UI/UX et portfolio

### Changements

- nouveau layout public sans dépendance CDN cassée ;
- remplacement du JavaScript NiceAdmin générique par un script minimal sans TinyMCE, Quill ni moteur de graphiques ;
- exclusion Git des sources SCSS, thèmes dupliqués, uploads historiques et bibliothèques frontend non chargées ; seuls les assets réellement utilisés sont publiés ;
- navigation latérale authentifiée adaptée au rôle ;
- accueil médical sobre, responsive et alimenté par les données ;
- formulaires, tables, cartes, badges, états vides et messages harmonisés ;
- CSS et icônes servis localement ;
- correction d’un débordement Bootstrap mobile lié aux gouttières `g-5` ;
- captures desktop et mobile générées depuis l’application réelle ;
- README, architecture et journal de migration.

### Fichiers principaux

- `templates/base.html.twig`, `templates/base_front.html.twig`, `templates/base_auth.html.twig`
- `templates/home1/home.html.twig`
- `templates/evenement/*`, `templates/stock/*`, `templates/rdv/*`
- `public/assets/css/clinic.css`, `public/assets/css/public.css`
- `docs/screenshots/*`
- `README.md`, `docs/ARCHITECTURE.md`

## Résumé des migrations

| Migration | Effet |
|---|---|
| `Version20230309070202` | Schéma historique initial |
| `Version20260806210000` | Planning, patient/médecin, disponibilités, index et contraintes uniques |
| `Version20260806211500` | Retrait des valeurs techniques utilisées pour la reprise de données |
| `Version20260806223000` | Consultations et prescriptions privées |
| `Version20260806223100` | Normalisation des noms d’index médicaux |
| `Version20260806233000` | Alignement DBAL 4 et retrait de Messenger inutilisé |
| `Version20260806234000` | Longueurs, nullabilité et intégrité des réclamations |

### Intervention manuelle requise

Aucune modification SQL manuelle n’est requise. Utiliser exclusivement :

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

Avant d’appliquer les migrations sur une base contenant des données non fictives, effectuer une sauvegarde et vérifier les doublons dans les tables de catégories et de participations. Les migrations n’effacent pas les rendez-vous, utilisateurs, consultations ou prescriptions existants.

## Vérifications réalisées

- lint PHP sur `src/` et `migrations/` ;
- validation du conteneur Symfony ;
- lint Twig et YAML ;
- validation du mapping et synchronisation du schéma Doctrine ;
- état des migrations à jour ;
- validation stricte et audit Composer ;
- contrôle HTTP des routes publiques et des zones par rôle ;
- suppressions GET refusées ;
- connexion et CSRF vérifiés ;
- contrôle navigateur desktop/mobile, chargement CSS et absence d’erreur JavaScript ;
- accès médical administrateur/réceptionniste refusé.

## Limites restantes

- vérification e-mail et récupération de mot de passe non implémentées ;
- documents médicaux privés non implémentés ;
- profils médecin/patient non séparés de l’entité `User` ;
- quelques noms historiques francophones/anglophones coexistent encore pour éviter une migration risquée ;
- les colonnes historiques `nom`, `dateR` et `idpatient` du rendez-vous sont conservées temporairement ;
- e-mail réel désactivé par défaut ;
- pas de tests automatisés ou de configuration DevOps, conformément au périmètre demandé.

## Messages de commit suggérés

1. `fix: stabilize legacy CRUD routes and secure destructive actions`
2. `feat: add authentication roles profiles and login throttling`
3. `feat: enforce appointment availability conflicts and permissions`
4. `feat: add private consultations and prescriptions`
5. `refactor: align Doctrine schema constraints and repositories`
6. `chore: upgrade Symfony 7.4 and supported dependencies`
7. `style: modernize responsive public and role-based interfaces`
8. `docs: add audit architecture setup and portfolio screenshots`
