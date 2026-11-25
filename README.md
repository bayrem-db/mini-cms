# Mini-CMS

Projet fil rouge de l'Atelier Framework Côté Serveur (Laravel 13, PHP 8.3+), 3e année MDW, ISET Sidi Bouzid.

Auteur : Bayrem, groupe MDW32.

## Description

Mini-CMS est une petite plateforme de publication écrite en Laravel 13 : pages publiques, back-office authentifié et API JSON. Le projet est corrigé depuis un clone propre, sur SQLite, en suivant uniquement ce README. Le dépôt est `https://github.com/bayrem-db/mini-cms.git` ; la version livrée est le tag `lab-08b`.

## Fonctionnalités

- Site public : accueil, `/a-propos` (Bayrem, MDW32), `/heure`, liste et détail des articles, archives par année, pages par catégorie et par tag.
- Back-office : création, modification, suppression et publication des articles ; gestion des catégories et des tags ; validation en français.
- Authentification manuelle : `/inscription`, `/connexion`, `/deconnexion`, avec case « se souvenir de moi ».
- Rôles `admin` et `author` : Gates `manage-categories` / `manage-tags`, `PostPolicy`, middleware `admin` et `Gate::before` dans `AppServiceProvider`.
- Défis conservés : `/mes-articles`, `/admin/utilisateurs` (changement de rôle ; un administrateur ne peut pas modifier son propre rôle).
- API JSON : lecture de `/api/posts`, `/api/categories`, `/api/tags` et de leurs pages détaillées ; filtres `?category=`, `?tag=`, `?per_page=` ; écritures protégées par jetons Sanctum et la même `PostPolicy` que le site.
- Défis S13/S14 conservés : `tags` dans les réponses, `body` uniquement sur la page d'un article, collection `docs/postman/mini-cms.postman_collection.json` et notes `notes/s13-defi.md` / `notes/s14-defi.md`.
- `Model::preventLazyLoading(! app()->isProduction())` en développement ; chargements explicites avec `with()` / `withCount()` ; Resources `PostResource`, `CategoryResource`, `TagResource`.
- Pages d'erreur 404 et 503 dans le layout ; messages de validation traduits en français.
- Suite Pest verte : 97 tests / 300 assertions.

## Installation

### Bash

```bash
git clone https://github.com/bayrem-db/mini-cms.git
cd mini-cms
composer install
npm install
npm run build
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate:fresh --seed
php artisan test
composer run dev
```

### PowerShell

```powershell
git clone https://github.com/bayrem-db/mini-cms.git
cd mini-cms
composer install
npm install
npm run build
copy .env.example .env
if (-not (Test-Path database\database.sqlite)) { New-Item database\database.sqlite -ItemType File }
php artisan key:generate
php artisan migrate:fresh --seed
php artisan test
composer run dev
```

`npm run build` est indispensable : le layout charge ses fichiers avec `@vite`, et les tests qui affichent une page échouent sur un clone qui n'a jamais été construit. Ouvrir ensuite http://localhost:8000 (adresse de l'application, différente de celle de Vite). Si Artisan demande de créer `database/database.sqlite`, répondre `yes`.

## Comptes de test

Après `php artisan migrate:fresh --seed` :

- Administrateur : `test@example.com` / `password` (rôle `admin`).
- Deux auteurs créés par `UserFactory`, avec le mot de passe `password` et le rôle `author`. Leurs adresses étant aléatoires, les afficher avec :
  `php artisan tinker --execute="echo App\Models\User::all()->pluck('email')->implode(PHP_EOL);"`
- Les jetons API s'obtiennent avec `POST /api/login` ; aucun jeton réel n'est conservé dans ce dépôt.

## API

### Endpoints

| Méthode | URI | Accès | Remarque |
|---|---|---|---|
| GET | `/api/posts` | public | articles publiés, paginés (`?per_page=` 1–50, défaut 5), filtres `?category=<slug>` et `?tag=<slug>` |
| GET | `/api/posts/{post}` | public | `body` uniquement ici ; brouillon 403, inconnu 404 |
| GET | `/api/categories` | public | `posts_count` (articles publiés) |
| GET | `/api/categories/{category}` | public | `posts_count` |
| GET | `/api/tags` | public | `posts_count` (articles publiés) |
| GET | `/api/tags/{tag}` | public | 404 JSON si inconnu |
| POST | `/api/login` | public | renvoie un jeton Sanctum (`plainTextToken`) |
| POST | `/api/logout` | `auth:sanctum` | révoque le jeton courant, 204 |
| POST | `/api/posts` | `auth:sanctum` | 201, mêmes Form Requests et `PostPolicy` que le site |
| PUT/PATCH | `/api/posts/{post}` | `auth:sanctum` + Policy | 401, 403, 404, 422 |
| DELETE | `/api/posts/{post}` | `auth:sanctum` + Policy | 204, 401, 403, 404 |

Les réponses de liste ont une clé `data` ; les listes paginées ajoutent `links` et `meta`. Les dates sont en ISO 8601 ; ni e-mail ni rôle des auteurs ne sont exposés. Un slug inconnu dans un filtre est refusé avec un message en français (422).

### Authentification par jeton

Le groupe de routes `api` n'utilise ni session ni cookie : chaque requête protégée présente le jeton dans l'en-tête `Authorization: Bearer <jeton>`.

```bash
curl.exe -X POST -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"email\":\"test@example.com\",\"password\":\"password\"}" http://localhost:8000/api/login
```

```json
{"token":"VOTRE_JETON","token_type":"Bearer"}
```

`VOTRE_JETON` est un placeholder, jamais un jeton réel. Le jeton brut n'est affiché qu'une fois ; seul son empreinte SHA-256 est stockée dans `personal_access_tokens`. Par défaut, il n'expire pas : révoquez-le avec `/api/logout` quand vous avez terminé.

### Codes d'erreur

- `401` : jeton absent, invalide ou révoqué (`auth:sanctum`).
- `403` : action refusée par `PostPolicy` (article d'un autre auteur, brouillon sur `show`).
- `404` : ressource inconnue, en JSON avec une clé `message`.
- `422` : validation (par exemple `per_page` hors de 1–50 ou slug inconnu), messages en français.

### Exemple curl.exe

```bash
curl.exe -X POST -H "Accept: application/json" -H "Authorization: Bearer VOTRE_JETON" http://localhost:8000/api/logout
```

La réponse 204 n'a pas de corps : la commande n'affiche rien quand le jeton est valide.

## Captures d'écran

### Page d'accueil

![Page d'accueil de Mini-CMS](screenshots/s02-accueil.png)

### Page Heure

![Page Heure](screenshots/s02-heure.png)

### Liste des routes

![Sortie de php artisan route:list](screenshots/s02-route-list.png)

### Page À propos

![Page A propos](screenshots/s02-a-propos.png)

### Accueil avec le layout

![Page d'accueil avec le layout](screenshots/s03-accueil.png)

### Liste des articles

![Liste des articles](screenshots/s03-articles.png)

### Détail d'un article

![Détail d'un article](screenshots/s03-article.png)

### Session 04

![Liste des articles](screenshots/s04-articles.png)

### Session 05

![Liste paginée des articles](screenshots/s05-articles.png)

![Formulaire avec erreurs de validation](screenshots/s05-formulaire.png)

### Session 06

![Formulaire de modification prérempli](screenshots/s06-modification.png)

![Confirmation de suppression](screenshots/s06-suppression.png)

### Session 07

![Liste des articles avec leur catégorie, leur auteur et leurs tags](screenshots/s07-liste-relations.png)

![Page d'un article avec ses tags et les articles de la même catégorie](screenshots/s07-detail-tags.png)

### Session 08

![Formulaire de modification avec la catégorie et les tags](screenshots/s08-formulaire-relations.png)

![Page des articles d'une catégorie](screenshots/s08-page-categorie.png)

### Session 09

![Page d'un article avec le message flash « Article créé. »](screenshots/s09-message-flash.png)

![Page Article introuvable pour un identifiant inexistant](screenshots/s09-article-introuvable.png)

### Session 10

Back-office des catégories et des tags, messages de validation en français, pages 404 et 503 dans le layout du site.

![Formulaire de catégorie envoyé vide, avec les messages de validation en français](screenshots/s10-validation.png)

![Page 404 du site pour une adresse inconnue](screenshots/s10-404.png)

### Session 11

![Barre de navigation d'un utilisateur connecté, avec le message « Vous êtes connecté. »](screenshots/s11-connexion.png)

![Page 403 quand un auteur ouvre la modification de l'article d'un autre auteur](screenshots/s11-interdit.png)

### Session 12

![Page 403 « Réservé aux administrateurs. » pour un compte auteur sur /admin/tags](screenshots/s12-admin-interdit.png)

![Barre de navigation d'un compte auteur, sans les liens Catégories et Tags](screenshots/s12-menu-auteur.png)

### Session 13

![Postman : réponse JSON de GET /api/posts?per_page=2, avec data, links et meta](screenshots/s13-postman-liste.png)

![Postman : réponse 422 de GET /api/posts?per_page=500, avec le message en français](screenshots/s13-postman-422.png)

Captures réalisées avec le navigateur et `curl.exe` (repli prévu par le codelab lorsque Postman n'est pas installé) : elles montrent les mêmes réponses API que Postman, annotées avec l'URL et le code HTTP.

### Session 14

![Postman : POST /api/posts avec un jeton Bearer, onglet Body, réponse 201 Created](screenshots/s14-postman-201.png)

![Postman : PUT sur l'article d'un autre auteur, réponse 403](screenshots/s14-postman-403.png)

Ces deux captures utilisent le même repli : réponses API réelles, annotées avec l'URL, l'onglet Body et le code HTTP. Aucun jeton n'apparaît dans les images ni dans la collection exportée.

## Choix d'architecture

### Pourquoi les écritures de l'API passent par PostPolicy

Je n'ai écrit qu'une seule règle pour décider qui peut modifier ou supprimer un article. Le site web et l'API l'appellent tous les deux. Si je change cette règle, les deux portes changent ensemble : l'API ne peut pas devenir un raccourci qui contourne les droits du site. La Form Request vérifie les données avant le contrôleur ; pour une modification, c'est son `authorize()` qui consulte la Policy, afin qu'un utilisateur non autorisé reçoive 403 et non 422. `destroy`, qui n'a pas de Form Request, commence par `Gate::authorize`.

### Pourquoi preventLazyLoading est activé

Quand une relation est lue dans une boucle sans avoir été chargée, Laravel fait une requête supplémentaire par élément : c'est le problème N+1. En développement, `Model::preventLazyLoading(! app()->isProduction())` transforme ce défaut silencieux en erreur visible. C'est pour cela que les contrôleurs chargent explicitement `with()` et comptent avec `withCount()`. L'option reste désactivée en production pour ne pas casser le site pour une relation oubliée.

### Pourquoi l'API utilise des jetons et non la session

Le groupe de routes `api` n'a ni session ni cookie. Ses clients ne sont pas forcément des navigateurs : Postman, `curl.exe`, un script ou une application mobile. Chaque requête protégée présente donc un jeton dans `Authorization: Bearer`. Ce jeton se révoque seul avec `/api/logout`, sans déconnecter les autres appareils, et les droits restent ceux de `PostPolicy`, identiques à ceux du site.

## Annexes conservées

### État actuel (tag lab-08b)

- `GET /api/posts`, `GET /api/categories`, `GET /api/tags` et leurs pages détaillées, en JSON.
- Filtres exacts par slug : `?category=<slug>`, `?tag=<slug>` ; `?per_page=` de 1 à 50, avec validation en français.
- `POST /api/login` renvoie un jeton Sanctum ; `POST /api/logout` le révoque.
- `POST`, `PUT/PATCH` et `DELETE /api/posts` protégés par `auth:sanctum`, avec les mêmes Form Requests et la même `PostPolicy` que le site (401, 403, 422).
- Tests Pest `ApiReadTest`, `ApiWriteTest` et `TagApiTest` : 97 tests verts.
- Authentification manuelle, Gates, middleware `admin`, `Gate::before`, défis `/mes-articles`, `/admin/utilisateurs` et collection Postman conservés.

### Données de démonstration

Après un clone, reconstruire la base avec `php artisan migrate:fresh --seed`. Attention : cette commande vide la base avant de la remplir.

### Routes disponibles

| Méthode | URI | Nom | Action |
|---|---|---|---|
| GET | `/` | `home` | `PageController@home` |
| GET | `/a-propos` | `about` | `PageController@about` |
| GET | `/heure` | `time` | `PageController@time` |
| GET | `/posts` | `posts.index` | `PostController@index` |
| GET | `/posts/archive/{year}` | `posts.archive` | `PostController@archive` |
| GET | `/posts/categorie/{category:slug}` | `posts.category` | `PostController@byCategory` |
| GET | `/posts/tag/{tag:slug}` | `posts.tag` | `PostController@byTag` |
| GET | `/posts/create` | `posts.create` | `PostController@create` |
| POST | `/posts` | `posts.store` | `PostController@store` |
| GET | `/posts/{post}` | `posts.show` | `PostController@show` |
| GET | `/posts/{post}/edit` | `posts.edit` | `PostController@edit` |
| PUT | `/posts/{post}` | `posts.update` | `PostController@update` |
| DELETE | `/posts/{post}` | `posts.destroy` | `PostController@destroy` |
| PATCH | `/posts/{post}/publication` | `posts.publication` | `PostController@togglePublication` |
| GET | `/admin/categories` | `admin.categories.index` | `CategoryController@index` |
| GET | `/admin/categories/create` | `admin.categories.create` | `CategoryController@create` |
| POST | `/admin/categories` | `admin.categories.store` | `CategoryController@store` |
| GET | `/admin/categories/{category}/edit` | `admin.categories.edit` | `CategoryController@edit` |
| PUT | `/admin/categories/{category}` | `admin.categories.update` | `CategoryController@update` |
| DELETE | `/admin/categories/{category}` | `admin.categories.destroy` | `CategoryController@destroy` |
| GET | `/admin/tags` | `admin.tags.index` | `TagController@index` |
| GET | `/admin/tags/create` | `admin.tags.create` | `TagController@create` |
| POST | `/admin/tags` | `admin.tags.store` | `TagController@store` |
| GET | `/admin/tags/{tag}/edit` | `admin.tags.edit` | `TagController@edit` |
| PUT | `/admin/tags/{tag}` | `admin.tags.update` | `TagController@update` |
| DELETE | `/admin/tags/{tag}` | `admin.tags.destroy` | `TagController@destroy` |
| GET | `/inscription` | `register` | `Auth\RegisterController@create` |
| POST | `/inscription` | `register.store` | `Auth\RegisterController@store` |
| GET | `/connexion` | `login` | `Auth\LoginController@create` |
| POST | `/connexion` | `login.store` | `Auth\LoginController@store` |
| POST | `/deconnexion` | `logout` | `Auth\LoginController@destroy` |
| GET | `/mes-articles` | `my-posts` | `PostController@myPosts` |
| GET | `/admin/utilisateurs` | `admin.users.index` | `UserController@index` |
| PATCH | `/admin/utilisateurs/{user}/role` | `admin.users.role` | `UserController@updateRole` |
| GET | `/api/posts` | `api.posts.index` | `Api\PostController@index` |
| GET | `/api/posts/{post}` | `api.posts.show` | `Api\PostController@show` |
| GET | `/api/categories` | `api.categories.index` | `Api\CategoryController@index` |
| GET | `/api/categories/{category}` | `api.categories.show` | `Api\CategoryController@show` |
| GET | `/api/tags` | `api.tags.index` | `Api\TagController@index` |
| GET | `/api/tags/{tag}` | `api.tags.show` | `Api\TagController@show` |
| POST | `/api/login` | `api.login` | `Api\AuthController@login` |
| POST | `/api/logout` | `api.logout` | `Api\AuthController@logout` |
| POST | `/api/posts` | `api.posts.store` | `Api\PostController@store` |
| PUT/PATCH | `/api/posts/{post}` | `api.posts.update` | `Api\PostController@update` |
| DELETE | `/api/posts/{post}` | `api.posts.destroy` | `Api\PostController@destroy` |

### Comparaison avec le starter kit Livewire

| Critère | Mini-CMS (authentification manuelle) | Starter kit Livewire |
|---|---|---|
| Routes d'authentification | `routes/web.php` (`/inscription`, `/connexion`, `/deconnexion`) | enregistrées par Fortify depuis son paquet ; absentes de `routes/web.php` |
| Inscription | `app/Http/Controllers/Auth/RegisterController.php` | `app/Actions/Fortify/CreateNewUser.php` (`Validator::make` puis `User::create`) et `resources/views/pages/auth/register.blade.php` |
| Connexion | `app/Http/Controllers/Auth/LoginController.php::store` (`Auth::attempt`, `regenerate`) | contrôleur de Fortify et `resources/views/pages/auth/login.blade.php` |
| Déconnexion | `app/Http/Controllers/Auth/LoginController.php::destroy` (`logout`, `invalidate`, `regenerateToken`) | route `logout` de Fortify ; les mêmes trois opérations dans `app/Livewire/Actions/Logout.php` |
| Limitation des tentatives | aucune | 5 tentatives par minute par e-mail et adresse IP (`app/Providers/FortifyServiceProvider.php`) |
| Mot de passe oublié | absent | `Features::resetPasswords()` dans `config/fortify.php` |
| Vérification de l'e-mail | absente | `Features::emailVerification()`, `MustVerifyEmail` sur `User`, middleware `verified` sur `/dashboard` |
| Double authentification | absente | `Features::twoFactorAuthentication()` et `Features::passkeys()` |
| Rôles et autorisations | colonne `role`, Gates, `PostPolicy`, middleware `admin` | aucun : à écrire soi-même |
| Tests | `AuthTest`, `AuthorizationTest`, `AdminAccessTest`, `PostVisibilityTest` | 10 fichiers dans `tests/Feature` (Auth, Settings, Dashboard) ; écrits avec PHPUnit dans le dépôt |

Ce que je garderais du kit dans un vrai projet : la limitation des tentatives, le mot de passe oublié et la vérification de l'e-mail, qui évitent de réécrire des flux longs et sensibles à sécuriser. Je garderais aussi ses écrans Livewire/Flux, mais j'écrirais moi-même les rôles, Gates et Policies, que Fortify ne fournit pas.
