# PI Mobile — Codename One

Application mobile académique développée en Java avec Codename One. Cette branche contient uniquement la partie mobile du projet PIDEV ; la partie Symfony reste isolée dans sa propre branche.

## Fonctionnalités

- Authentification, inscription et gestion du profil
- Gestion des utilisateurs
- Consultation et gestion des événements
- Gestion des catégories
- Création et suivi des réclamations
- Recherche et export PDF des réclamations

## Technologies

- Java 8 pour le niveau source historique
- Codename One
- NetBeans et Ant pour la structure historique
- API Symfony pour les données distantes
- Ressources UI Codename One dans `theme.res`

## Structure

```text
src/com/mycompany/myapp/
├── entities/           Modèles métier
│   ├── gui/            Écrans Codename One
│   └── services/       Appels vers l'API Symfony
├── utils/              Configuration partagée
└── MyApplication.java  Point d'entrée
```

## Prérequis

- JDK 17 recommandé pour l'environnement de développement
- Apache Ant pour le build historique
- Codename One Simulator ou un IDE compatible
- Backend Symfony démarré et accessible

## Exécution du build historique

Le projet utilise encore l'ancienne structure NetBeans/Ant. Les artefacts `build/` et `dist/` ne sont pas versionnés.

Après restauration des dépendances Codename One compatibles :

```powershell
ant clean jar
java -jar .\dist\PI_Mobile.jar
```

Le build contient encore des références locales à d'anciennes bibliothèques. Une migration vers Maven est prévue avant de considérer le build comme reproductible.

## Configuration de l'API

L'URL du backend est définie dans `src/com/mycompany/myapp/utils/Statics.java`.

La valeur `127.0.0.1` fonctionne uniquement avec le simulateur lancé sur la même machine. Pour un téléphone physique, utiliser une URL HTTPS accessible depuis l'appareil.

Ne jamais placer de mot de passe, token, clé API ou identifiant SMTP dans le code mobile.

## Sécurité

- Aucun secret de signature ou mot de passe ne doit être ajouté à `codenameone_settings.properties`.
- L'envoi d'e-mails doit être réalisé par le backend Symfony.
- Les identifiants ne doivent pas être envoyés dans les paramètres d'URL.
- La future API devra utiliser HTTPS et une authentification par jeton.
- Les jetons mobiles devront être conservés dans le stockage sécurisé de la plateforme.

## État actuel et limitations

- Projet Codename One hérité de 2023
- Build Ant non reproductible sans remise à niveau des dépendances
- Interface encore académique et non homogène
- Contrat API mobile à aligner avec le backend Symfony modernisé
- Gestion des rôles et autorisations à renforcer
- Modules cliniques non encore présents dans cette version mobile

## Modernisation prévue

1. Migrer la structure Ant vers Maven sans réécrire les fonctionnalités.
2. Centraliser les appels réseau dans un client API sécurisé.
3. Ajouter l'authentification et la navigation par rôle.
4. Aligner les modèles mobiles avec l'API Symfony.
5. Moderniser le thème, la navigation et les formulaires.
6. Ajouter progressivement les modules cliniques utiles au mobile.

## Données de démonstration

Utiliser uniquement des comptes et données fictifs. Ne jamais stocker de vraies informations médicales ou personnelles dans le dépôt.

## Licence

Projet académique réalisé dans le cadre du cursus d'ingénierie logicielle à ESPRIT.
