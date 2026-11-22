# Session 11 - Défi

/mes-articles ne montre que mes articles, brouillons compris : oui
Un visiteur qui ouvre /mes-articles arrive sur /connexion : oui
Le cookie remember_web_ apparaît quand la case est cochée : oui
Les deux nouveaux tests passent et php artisan test est vert : oui

Pourquoi partir de $request->user()->posts() plutôt que de Post::where('user_id', $request->user()->id) ?
La relation part déjà de l'utilisateur de la session : aucun champ de formulaire ni paramètre d'URL ne peut la rediriger vers un autre auteur. Elle garde aussi les brouillons, car aucun filtre published n'est ajouté.

Pourquoi la case remember ne doit-elle pas être dans le tableau passé à Auth::attempt() ?
Auth::attempt() traite chaque clé du tableau, sauf password, comme une colonne de la table users. Une clé remember provoquerait une erreur SQL « no such column: remember ». Le cookie doit être passé en second argument, séparément des identifiants.
