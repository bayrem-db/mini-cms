# Session 03 - Routes, contrôleurs et layout

## Défi

URL produite par route('about') :

http://localhost:8000/a-propos

URL produite par route('posts.show', 'routes-et-controleurs') :

http://localhost:8000/posts/routes-et-controleurs

URL produite par route('posts.archive', 2025) :

http://localhost:8000/posts/archive/2025

Pourquoi /posts/archive/abc et /posts/article-inconnu répondent toutes deux 404, et quelle ligne de code en est responsable dans chaque cas ?

/posts/archive/abc : la contrainte `->whereNumber('year')` dans `routes/web.php` rejette la route avant même que le contrôleur ne soit appelé ; Laravel cherche une autre route puis répond 404.
/posts/article-inconnu : l'URL respecte `->where('slug', '[a-z0-9-]+')`, donc la route correspond ; c'est `abort(404)` dans `PostController::show()` qui répond 404 parce que le slug n'existe pas dans le tableau.

Que se passe-t-il si une vue appelle route('posts.show') sans paramètre ?

Laravel lève une erreur `Missing required parameter for [Route: posts.show] [URI: posts/{slug}] [Missing parameter: slug]` : la route attend le paramètre `{slug}`.
