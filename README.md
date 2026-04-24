# API Laravel — Backend MVC (PostgreSQL + Sanctum)

API REST en **Laravel** structurée en **MVC**, avec :
- **PostgreSQL** (obligatoire)
- **Eloquent ORM** + relations (`User` 1..N `Task`)
- **Validation via FormRequest**
- **API JSON**
- **Authentification par token avec Laravel Sanctum**

## Prérequis

- PHP **8.3+** avec extensions courantes Laravel
- PostgreSQL en local (ou distant) + extension PHP `pdo_pgsql`
- [Composer](https://getcomposer.org/)

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Vérifier dans `.env` : `DB_CONNECTION=pgsql` et renseigner `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

## Lancer le serveur

```bash
php artisan serve
```

Base URL locale : `http://127.0.0.1:8000` (préfixe API : `/api`).

## Endpoints

| Méthode | URL | Auth |
|--------|-----|------|
| `POST` | `/api/register` | Non |
| `POST` | `/api/login` | Non (throttle login) |
| `POST` | `/api/logout` | Bearer token |
| `GET` | `/api/me` | Bearer token |
| `GET` | `/api/tasks` | Bearer token |
| `POST` | `/api/tasks` | Bearer token |
| `GET` | `/api/tasks/{id}` | Bearer token |
| `PUT` / `PATCH` | `/api/tasks/{id}` | Bearer token |
| `DELETE` | `/api/tasks/{id}` | Bearer token |

**Postman** : après login ou register, copier le champ `token` de la réponse JSON, puis **Authorization → Bearer Token**.

En-têtes utiles : `Accept: application/json`, `Content-Type: application/json` pour le corps JSON.

## Architecture demandée (checklist)

- **MVC** : contrôleurs dans `app/Http/Controllers/Api`, modèles dans `app/Models`
- **PostgreSQL** : connexion par défaut via `DB_CONNECTION=pgsql`
- **Eloquent & Relations** : `User::notes()` (`hasMany`) et `Note::user()` (`belongsTo`) pour la ressource Task
- **Validation FormRequest** :
  - `RegisterRequest`, `LoginRequest`
  - `StoreNoteRequest`, `UpdateNoteRequest`
- **API JSON** : réponses via `response()->json(...)` et API Resources (`UserResource`, `NoteResource`)
- **Sanctum** : login/register génèrent un token, routes protégées par `auth:sanctum`

## Format des données Task

La ressource renvoyée en JSON contient :
- `id`
- `user_id`
- `title`
- `description`
- `statu` (valeurs supportées : `pending`, `in_progress`, `done`)

## Tests

```bash
php artisan test
```

## Données (PostgreSQL)

Aperçu rapide :

```bash
php artisan db:show
php artisan db:table notes
```

## Licence

Projet éducatif — même licence que le squelette Laravel (MIT) sauf mention contraire.
