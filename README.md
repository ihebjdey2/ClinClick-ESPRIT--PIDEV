# PIDEV Desktop Java

Application desktop Java/JavaFX pour un projet académique PIDEV. Le dépôt contient une application client lourde organisée autour des écrans FXML, des services métier et d’un accès JDBC à MySQL.

## Vue d’ensemble

Le projet regroupe plusieurs modules métier déjà présents dans le code source:

- gestion des utilisateurs et de l’authentification
- gestion des événements
- gestion des catégories et articles
- gestion des réclamations et réponses
- gestion des rendez-vous / `rdv`
- génération ou export de documents PDF
- envoi d’e-mails via JavaMail
- intégrations utilitaires comme la base de données, SMS et hash de mot de passe

## Technologies

- Java
- JavaFX / FXML
- NetBeans / Ant
- JDBC
- MySQL
- JavaMail
- Apache POI
- iText PDF
- Apache HttpClient

## Point d’entrée

Le projet démarre désormais sur `GUI.Main`, qui charge `src/GUI/Login.fxml`.

## Configuration requise

- JDK compatible avec le projet Java 8 source level
- JavaFX disponible dans votre environnement ou IDE
- MySQL
- NetBeans ou un IDE capable d’ouvrir un projet Ant

## Configuration de la base de données

Le connecteur principal est défini dans `src/tools/MaConnection.java`:

```java
jdbc:mysql://localhost:3306/userrdb
user = root
password = ""
```

Adaptez ces valeurs à votre environnement local avant exécution.

## Configuration e-mail

L’envoi d’e-mails n’utilise plus de secret codé en dur. Configurez plutôt les variables d’environnement suivantes avant d’exécuter les fonctionnalités de mail:

- `SMTP_USERNAME`
- `SMTP_PASSWORD`
- `SMTP_HOST` (optionnel, valeur par défaut: `smtp.gmail.com`)
- `SMTP_PORT` (optionnel, valeur par défaut: `587`)

## Structure du projet

- `src/GUI` : écrans JavaFX, contrôleurs et ressources visuelles
- `src/entity` : entités métier et helpers
- `src/services` : services d’accès aux données et logique applicative
- `src/tools` : connexion DB et utilitaires
- `nbproject` : configuration NetBeans / Ant

## Lancement

Depuis NetBeans:

1. Ouvrez le projet.
2. Vérifiez les bibliothèques dans `src/lib` et la configuration MySQL.
3. Lancez le projet avec `GUI.Main` comme classe principale.

Depuis la ligne de commande, utilisez la configuration Ant du projet si votre environnement est prêt.

## Notes de sécurité

- Aucun mot de passe ne doit rester codé en dur dans le dépôt.
- Les identifiants MySQL et SMTP doivent être fournis localement.
- Les fichiers générés, binaires et artefacts de compilation ne doivent pas être versionnés.

## Configuration SMS

Si vous utilisez le helper SMS, configurez ces variables d’environnement en local:

- `TWILIO_ACCOUNT_SID`
- `TWILIO_AUTH_TOKEN`
## Remarques

Ce projet reste un projet académique et contient du code historique. La branche publiée sert de base propre pour une modernisation progressive.
