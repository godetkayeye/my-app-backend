# API Laravel — Authentification & Notes

API REST avec **Laravel** et **Laravel Sanctum** (tokens). Chaque utilisateur ne voit et ne modifie que **ses propres notes** (`user_id` + policies).

## Prérequis

- PHP **8.3+** avec extensions courantes Laravel
- Pour SQLite : `pdo_sqlite` et `sqlite3` (ex. paquet `php8.4-sqlite3` sur Ubuntu)
- [Composer](https://getcomposer.org/)

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # si le fichier n’existe pas encore
php artisan migrate
```

Vérifier dans `.env` : `DB_CONNECTION=sqlite` (le fichier par défaut est `database/database.sqlite`).

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
| `GET` | `/api/notes` | Bearer token |
| `POST` | `/api/notes` | Bearer token |
| `GET` | `/api/notes/{id}` | Bearer token |
| `PUT` / `PATCH` | `/api/notes/{id}` | Bearer token |
| `DELETE` | `/api/notes/{id}` | Bearer token |

**Postman** : après login ou register, copier le champ `token` de la réponse JSON, puis **Authorization → Bearer Token**.

En-têtes utiles : `Accept: application/json`, `Content-Type: application/json` pour le corps JSON.

## Tests

```bash
php artisan test
```

## Données (SQLite)

Fichier : `database/database.sqlite`. Aperçu rapide :

```bash
php artisan db:show
php artisan db:table notes
```

## Licence

Projet éducatif — même licence que le squelette Laravel (MIT) sauf mention contraire.
