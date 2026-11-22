# Session 02 - Git et GitHub

Réponses rédigées avec mes propres mots, puis comparées au corrigé de la page.

## 1. Index et commit

Réponse : `git add <fichier>` fait passer le fichier du répertoire de travail vers l'index (la zone de préparation). `git commit` enregistre ensuite le contenu de l'index dans le dépôt local sous forme d'un commit. `git push` publie enfin les commits sur le dépôt distant `origin`.

## 2. Branche et tag

Réponse : Une branche est une étiquette mobile : elle avance automatiquement à chaque nouveau commit. Un tag est fixé une fois pour toutes sur un commit précis : il ne bouge plus. Ici `lab-01` désigne le commit de création du projet et `lab-01b` le commit de fin de session.

## 3. Le fichier .env

Réponse : `.env` contient `APP_KEY` (la clé de chiffrement générée par `php artisan key:generate`) et les réglages propres à la machine. Il peut contenir des secrets en production, donc il ne doit jamais être versionné ni poussé. Un autre développeur copie `.env.example` vers `.env`, puis lance `php artisan key:generate` pour obtenir sa propre clé.

## 4. Le dossier vendor/

Réponse : `vendor/` contient toutes les dépendances PHP installées par Composer : des milliers de fichiers entièrement générés, inutiles à versionner. Le fichier `composer.lock` enregistre les versions exactes ; `composer install` reconstruit `vendor/` à l'identique sur toute machine.

## 5. Git Credential Manager

Réponse : Git Credential Manager range le jeton GitHub dans le Gestionnaire d'identification Windows, sous l'entrée `git:https://github.com`. Sur un poste de l'université, il faut supprimer cette entrée en fin de séance : la personne suivante utilisant la même session Windows pourrait sinon pousser en mon nom. Sur un ordinateur personnel, on peut la conserver pour ne pas avoir à se reconnecter.

## 6. Historique depuis lab-01

Nombre de commits depuis lab-01 : 5 avant ce dernier commit (Ajouter les notes Git de la session 02, Ajouter la page a-propos, Ajouter les captures d'écran de la session 02, Ajouter le README du projet, Documenter les routes dans le README).

Fichiers modifiés depuis lab-01 : `notes/s02-git.md`, `routes/web.php`, `resources/views/a-propos.blade.php`, les quatre fichiers de `screenshots/` et `README.md` (modifié une deuxième fois par la partie 1 du défi).

Ce que `git tag -n` affiche pour lab-01 : `lab-01` suivi du message du commit qu'il désigne, soit `Initialiser le projet Mini-CMS`.
