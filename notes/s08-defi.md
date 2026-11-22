# Session 08 - Défi

La route posts.tag utilise {tag:slug} : oui
La page d'un tag n'affiche ni brouillon ni article d'un autre tag : oui
Les tags des cartes sont des liens : oui
L'accueil n'affiche que les tags qui ont au moins un article publié : oui
Les trois nouveaux tests passent et php artisan test est vert : oui

Pourquoi filtrer les tags vides après get() plutôt que d'afficher (0) ?
Un tag sans article publié mènerait à une page vide : on ne l'affiche donc pas. Filtrer après get() évite une condition SQL de plus dans withCount et garde une seule requête pour tous les tags.

Combien de requêtes ajoute la liste des tags populaires, et pourquoi pas une par tag ?
Une seule requête : withCount place la sous-requête de comptage dans la requête des tags. La base calcule tous les comptes en une fois ; le tri et le filtre se font ensuite en mémoire sur la collection.

Pourquoi aucune migration, aucun modèle et aucun test n'a changé ?
Eloquent et le constructeur de schéma traduisent les mêmes instructions pour chaque pilote : seule la connexion change. Le code reste portable car il n'utilise aucune requête SQL brute, compare les booléens à true ou false, trie explicitement chaque liste et compare les slugs exactement. Le bonus PostgreSQL est facultatif ; le tag lab-04b reste sur SQLite.
