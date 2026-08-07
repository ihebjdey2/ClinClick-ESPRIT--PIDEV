# Audit initial — CliniClic

Date de l'audit : 6 août 2026

> Ce document est un instantané réalisé avant les modifications. Pour l’état livré, consulter le [journal de modernisation](IMPLEMENTATION_LOG.md) et l’[architecture actuelle](ARCHITECTURE.md).

## 1. Résumé du projet actuel

CliniClic est une application académique Symfony qui regroupe plusieurs démonstrations CRUD : stocks médicaux, catégories, rendez-vous, événements, participations, réclamations et réponses. Elle contient un modèle `User`, mais pas de parcours d'authentification fonctionnel ni de profils métier patient, médecin ou réceptionniste.

Le projet démarre et Doctrine accède correctement à MySQL. Le schéma courant est synchronisé avec les métadonnées et l'unique migration est appliquée. La base de développement inspectée est vide. Plusieurs pages restent toutefois en erreur et la majorité des fonctions métier n'est pas protégée.

## 2. Versions et dépendances

| Élément | Version détectée | État |
|---|---:|---|
| PHP local | 8.2.12 | Fonctionnel, support de sécurité PHP jusqu'au 31 décembre 2026 |
| Symfony | 6.1.12 | Non maintenu depuis janvier 2023 |
| Doctrine ORM | 2.14.1 | Branche non maintenue |
| DoctrineBundle | 2.8.3 | Ancien |
| Twig | 3.5.1 | Ancien |
| KnpPaginatorBundle | 6.1.1 | À vérifier avant upgrade |
| VichUploaderBundle | 2.1.0 | Présent mais configuration commentée/incomplète |
| Dompdf | 2.0.3 | Ancien, audit de sécurité Composer requis |

Composer n'est pas installé sur la machine et seul le répertoire `vendor/` existant permet l'exécution. Aucun upgrade de dépendances ne doit donc être effectué avant l'installation de Composer et l'exécution de `composer validate`, `composer audit` et d'une résolution à blanc.

### Cible recommandée

- PHP 8.4 pour conserver une marge de support confortable.
- Symfony 6.4 LTS comme étape de compatibilité, puis Symfony 7.4 LTS comme cible 2026.
- Doctrine ORM 3.x après migration du code et validation des bundles.
- Suppression progressive de SensioFrameworkExtraBundle et Doctrine Annotations après migration complète vers les attributs PHP.

Références officielles :

- https://symfony.com/releases
- https://symfony.com/releases/7.4
- https://www.php.net/supported-versions.php
- https://www.doctrine-project.org/projects/orm.html

## 3. Modules et fonctions existants

- Page d'accueil publique basée sur un thème médical Bootstrap.
- Tableau de bord administratif statique basé sur NiceAdmin.
- Gestion du stock et de catégories de stock.
- Gestion de rendez-vous et de catégories de rendez-vous.
- Gestion d'événements, catégories et participations.
- Gestion de réclamations, catégories et réponses.
- Recherche simple de rendez-vous et de réclamations.
- Pagination Knp sur quelques listes, souvent après chargement complet en mémoire.
- Export PDF des réclamations.
- Envoi de courriel de démonstration.
- API JSON événement rudimentaire.

Fonctions cliniques absentes : profils médecins/patients/réceptionnistes, spécialités, départements, disponibilités, consultations, dossiers médicaux, prescriptions, historique médical, documents médicaux privés, notifications métier, facturation et paiement.

## 4. Entités et relations existantes

- `User` : courriel unique, rôles, mot de passe, identité, date de naissance et genre.
- `Categorie` 1—N `Stock`.
- `CategoryR` 1—N `RDV`.
- `Category` 1—N `Evenement`.
- `User` 1—N `Participer` et `Evenement` 1—N `Participer`, mais sans collections inverses ni unicité du couple.
- `CategorieReclamation` 1—N `Reclamation`.
- `Reclamation` 1—1 `Reponse`.

Anomalies importantes :

- `RDV.idpatient` est un entier brut et non une relation Doctrine.
- `RDV` n'a ni médecin, ni heure, ni durée, ni statut.
- Plusieurs relations obligatoires côté métier sont nullables en base.
- La suppression d'une `Reponse` peut supprimer la `Reclamation` associée à cause d'un cascade remove inversé.
- Aucune contrainte unique n'empêche une double participation.
- Aucun champ `createdAt`/`updatedAt` n'assure la traçabilité.
- Les statuts sont absents ou représentés par un booléen nullable (`Reclamation.etat`).
- Les suppressions en cascade et `orphanRemoval` ne sont pas justifiées par les règles métier.

## 5. Rôles et autorisations existants

- `User::getRoles()` ajoute uniquement `ROLE_USER`.
- Le provider de sécurité est un provider en mémoire vide, pas `UserRepository`.
- Aucun formulaire de connexion, authenticator ou logout n'est configuré.
- Aucun `access_control`, voter, `IsGranted` ou contrôle de propriété n'a été détecté.
- Les rôles `ROLE_ADMIN`, `ROLE_DOCTOR`, `ROLE_RECEPTIONIST` et `ROLE_PATIENT` ne sont pas implémentés.

Conséquence : les écrans administratifs, listes nominatives, suppressions et mutations API sont accessibles sans authentification.

## 6. Problèmes techniques critiques

- `/categorie`, `/addCategorie`, `/reponse` et `/evenement` retournent actuellement une erreur 500.
- Des contrôleurs appellent `ManagerRegistry` comme une fonction (`$doctrine()`), ce qui casse plusieurs traitements.
- `CategorieType` référence une propriété `type` inexistante.
- `ReclamationType` utilise la mauvaise entité de catégorie et un `choice_label` inexistant.
- La liste d'événements utilise une variable non initialisée lorsque la base est vide.
- `CategoryRController` mélange catégories et rendez-vous, effectue un faux appel de repository et supprime la mauvaise entité.
- Le choix de type de stock redirige toujours vers le même formulaire.
- L'export PDF ne retourne pas une réponse Symfony propre.
- Plusieurs entités manquantes ne sont pas gérées et causent des erreurs au lieu de 404 contrôlées.
- 38 attributs de route sur 60 ne contraignent pas la méthode HTTP.

## 7. Risques de sécurité et confidentialité

Priorité critique :

- Absence totale d'authentification et d'autorisation effective.
- Suppressions par liens GET, souvent sans jeton CSRF.
- API publique autorisant création, modification et suppression.
- Participation à un événement avec l'utilisateur ID 15 codé en dur.
- Adresses personnelles codées en dur dans trois contrôleurs.
- Route de démonstration permettant de déclencher un courriel via une requête GET.
- Valeurs sensibles présentes dans `.env`, alors que `.env` n'est pas ignoré et qu'aucun `.env.example` n'existe.
- Upload dans `public/images` avec nom basé sur `uniqid()` et sans validation MIME/taille explicite.
- Aucune vérification de propriété des rendez-vous, réclamations ou données personnelles.
- Données d'événements injectées dans JavaScript avec `|raw`, créant un risque XSS.
- L'environnement par défaut est `dev`, donc les erreurs affichent chemins, traces et détails internes.

Points positifs : Doctrine paramètre les requêtes de recherche observées, l'auto-échappement Twig est actif hors filtre `raw`, le hasher de mot de passe Symfony est configuré sur `auto`, et les CRUD générés `Category`/`Evenement` utilisent un jeton CSRF pour leur formulaire de suppression.

## 8. Problèmes d'architecture

- Aucune classe de service métier.
- Logique d'accès aux données, upload, courriel, recherche et règles dans les contrôleurs.
- Contrôleurs dépendant largement de `ManagerRegistry` au lieu des repositories et de services ciblés.
- Repositories presque entièrement composés de code généré commenté.
- Requêtes de recherche écrites dans les contrôleurs.
- Nommage incohérent en français/anglais, routes et classes aux conventions variables.
- Absence de types stricts, types de retour et propriétés typées cohérents.
- Code mort, imports inutiles, commentaires de génération et vue NiceAdmin complète inutilisée.
- Gestion des exceptions et messages utilisateur incohérente.

## 9. Problèmes de base de données

- Modèle de rendez-vous insuffisant pour gérer horaires, médecins, disponibilités et conflits.
- Clés étrangères métier manquantes, notamment rendez-vous—patient et rendez-vous—médecin.
- Nullabilité trop permissive sur plusieurs relations.
- Index uniquement générés pour certaines clés étrangères ; aucun index métier pour dates/statuts/listes.
- Aucune unicité sur une participation utilisateur—événement.
- Aucune date d'audit ni historique de statut.
- Relation `Reponse` → `Reclamation` avec cascade remove dangereuse.
- Aucun mécanisme de verrouillage/transaction pour éviter les réservations concurrentes.
- Toutes les tables inspectées sont vides ; aucune donnée patient réelle n'a été trouvée.

## 10. Problèmes UI/UX et frontend

- Deux thèmes Bootstrap distincts sont assemblés sans système de design commun.
- La page publique duplique un layout complet au lieu d'étendre un template partagé.
- 739 fichiers publics pour 38,6 Mo, dont sources SCSS/TypeScript, source maps et bibliothèques non utilisées.
- Assets locaux et CDN mélangés ; versions Bootstrap/jQuery incohérentes et chargements dupliqués.
- Deux images CSS sont référencées sous de mauvais noms.
- Navigation, profil, messages, statistiques et contenus sont largement codés en dur.
- Une seule table utilise un conteneur responsive parmi 17 tables.
- Très peu d'états vides, messages de confirmation ou retours d'erreur cohérents.
- Actions exprimées uniquement par des icônes/anglais et cibles tactiles irrégulières.
- Formulaires sans convention commune, aide contextuelle ou indicateur de champs obligatoires.
- Scripts AJAX dupliqués directement dans Twig et URLs construites manuellement.
- Seule une page d'erreur métier existe ; aucune page Symfony cohérente pour 403/404/500.

## 11. Code inutilisé ou dupliqué

- `templates/index.html.twig` : copie NiceAdmin autonome de plus de 1 100 lignes, non rendue par un contrôleur.
- `templates/new.html.twig` et `templates/tablesCategorie.html.twig` semblent orphelins.
- `MyControllerEmail` duplique le contrôleur mailer et n'expose aucune route.
- Méthodes de repository générées commentées dans presque tous les repositories.
- `home1/home.html.twig` duplique une grande partie de `base_front.html.twig`.
- Scripts de recherche rendez-vous répétés trois fois dans la même vue.
- Assets frontend vendoriés en double dans plusieurs arborescences.

La suppression de ces éléments sera différée jusqu'à la confirmation qu'aucun flux ne les utilise.

## 12. Plan priorisé

### Phase 1 — Stabilisation et sécurité immédiate

- Réparer les écrans et traitements actuellement cassés.
- Corriger les méthodes HTTP, ajouter CSRF aux mutations et supprimer les suppressions GET.
- Neutraliser l'injection JavaScript et sécuriser le premier upload existant.
- Retirer les identifiants personnels codés en dur au profit de variables d'environnement.
- Créer `.env.example`, protéger `.env` et ajouter des pages d'erreur cohérentes.
- Corriger les références CSS restantes et les défauts de base responsive/accessibilité.

### Phase 2 — Authentification et RBAC

- Connecter Security à `UserRepository`.
- Ajouter login/logout et création contrôlée de comptes fictifs.
- Introduire les quatre rôles, hiérarchie et règles d'accès.
- Séparer routes publiques et back-office.
- Ajouter des voters dès que les relations de propriété sont disponibles.

### Phase 3 — Modèle clinique et rendez-vous

- Introduire des profils simples et explicables pour patient, médecin et réceptionniste.
- Ajouter spécialités, disponibilités, date/heure/durée/statut du rendez-vous.
- Remplacer l'identifiant patient brut par des relations Doctrine sûres.
- Créer une migration additive préservant les données.
- Ajouter `AppointmentService`, enum de statut et détection transactionnelle des conflits.

### Phase 4 — Architecture métier et données

- Extraire services, requêtes de repositories et règles de validation.
- Corriger relations/cascades/nullabilité et ajouter index/contraintes.
- Ajouter timestamps et traçabilité minimale respectueuse de la vie privée.
- Optimiser pagination, filtres, dashboard et requêtes N+1.

### Phase 5 — Interface professionnelle

- Unifier layouts et système visuel médical.
- Créer des composants Twig réutilisables.
- Moderniser toutes les listes, formulaires, états vides et messages.
- Rendre navigation, tableaux et actions réellement responsives et accessibles.
- Alimenter les dashboards par des agrégats réels selon le rôle.

### Phase 6 — Upgrade contrôlé

- Installer Composer et exécuter validation/audit.
- Mettre à niveau vers Symfony 6.4 et corriger toutes les dépréciations.
- Vérifier/remplacer les bundles incompatibles.
- Passer vers PHP 8.4, Symfony 7.4 LTS et une version Doctrine 3.x compatible.
- Valider schéma, routes, conteneur et parcours manuels après chaque palier.

### Phase 7 — Documentation portfolio

- README complet, architecture, rôles, règles métier et commandes reproductibles.
- Configuration fictive, comptes de démonstration et captures d'écran sans données sensibles.
- Nettoyage prudent du code mort, des assets générés et des fichiers temporaires.
- Historique clair des migrations et limites connues.

Les tests automatisés, CI/CD, Docker additionnel, déploiement et monitoring sont explicitement hors périmètre conformément à la demande.
