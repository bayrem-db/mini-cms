# Session 14 - Défi

Connexion enregistre le jeton toute seule : oui
Créer un article répond 201 sans copier le jeton : oui
Le fichier exporté ne contient aucun jeton : oui

Le dépôt est public. Que risquez-vous si le fichier exporté contient un jeton, et comment l'annuler ?
N'importe qui peut agir en votre nom tant que le jeton n'est pas révoqué : par défaut, un jeton Sanctum n'expire jamais. Si un jeton se retrouve dans un dépôt public, il faut le révoquer avec POST /api/logout en présentant ce jeton ; si la valeur n'est plus disponible, supprimer sa ligne dans personal_access_tokens. Il faut ensuite le retirer du fichier, le révoquer puis créer un nouveau jeton. Un jeton déjà publié doit être considéré comme compromis, même après suppression du fichier.

Pourquoi les écritures de l'API passent-elles par les Form Requests et PostPolicy plutôt que par des règles écrites dans Api\PostController ?
Une règle pour deux portes : la validation et les droits sont déjà définis pour le site, l'API les réutilise sans les recopier. Un changement dans une Form Request ou dans PostPolicy s'applique en même temps au navigateur et aux clients JSON, donc les deux ne peuvent pas se contredire. L'ordre est aussi maîtrisé : le middleware renvoie 401 sans jeton, la Policy 403 avant la validation, puis les règles 422, et le contrôleur ne reçoit que des données valides et autorisées.

La collection `docs/postman/mini-cms.postman_collection.json` utilise deux variables (`base_url`, `token`). La requête « Connexion » remplit `token` dans un script post-réponse ; « Créer un article » et « Déconnexion » héritent de l'autorisation Bearer `{{token}}`. La valeur exportée du jeton est vide.
