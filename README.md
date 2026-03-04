# Translation Service

A small Laravel‑based JSON API for storing, searching and exporting
text translations. Each translation has a locale, a key, content and tags; the
service supports full‑text searching, CSV export and token authentication.

## What is it?

* Laravel 10 application
* API protected with Laravel Sanctum
* Translations keyed by locale/tag, searchable by key/content
* Swagger/OpenAPI docs available at `/docs` (full spec also in `API-DOCUMENTATION.md`)
* Docker‑ready (PHP‑FPM + Nginx + MySQL) or runs on XAMPP/localhost

## How to set it up

1. Clone the repo:
   ```bash
   git clone <url> translation-service
   cd translation-service
   cp .env.example .env
   php artisan key:generate
   ```

2. Configure `.env` depending on environment (see below).

3. Run the database migrations and seeders:

```bash
# apply fresh schema and run all seeders
php artisan migrate:fresh --seed

# or execute a specific seeder manually
php artisan db:seed --class=TranslationSeeder

# quickly generate thousands of test translations
php artisan app:seed-translations 5000
```

4. You can start the built‑in PHP server for local development without Docker:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

The API will then be accessible at http://localhost:8000 (same port
used by the Docker configuration).  Docker instructions follow.

### Docker

```bash
# in .env:
DB_HOST=db
DB_PORT=3306
DB_DATABASE=translation
DB_USERNAME=user
DB_PASSWORD=secret

# start containers
docker-compose up -d --build
# initialise database
docker-compose exec app bash -lc "composer install && php artisan migrate:fresh --seed"
```

### XAMPP / native PHP

* Point your webserver at the `public/` directory
* Set DB vars to `127.0.0.1:3306` (`root`/no password by default)
* Run migrations using XAMPP's PHP CLI:
  ```bash
  php artisan migrate:fresh --seed
  ```

## API endpoints

| Method | URI                             | Description                     |
|--------|----------------------------------|---------------------------------|
| POST   | `/api/login`                    | obtain token                    |
| POST   | `/api/logout`                   | revoke token                    |
| GET    | `/api/translations`             | list/search translations        |
| POST   | `/api/translations`             | create translation              |
| GET    | `/api/translations/{id}`        | show translation                |
| PUT    | `/api/translations/{id}`        | update translation              |
| DELETE | `/api/translations/{id}`        | delete translation              |
| GET    | `/api/export/{locale}`          | export translations for locale  |

**Search filters** (`/api/translations`):
`locale`, `key`, `content`, `tag`, plus `page` for pagination.

## Documentation

* **Swagger UI** – `http://localhost:8000/docs`
* **Raw OpenAPI spec** – `http://localhost:8000/api-docs`

Use the "Authorize" button to supply your bearer token and test
requests from the browser.

## Testing

```bash
php artisan test --coverage
``` 

Coverage currently exceeds 95 % and includes unit/feature tests for all
controllers, repositories, services and models.

## Docker details

The repository contains:

* `Dockerfile` – PHP‑FPM build
* `docker-compose.yml` – services: `app` (PHP), `web` (Nginx), `db`
* `docker/nginx/conf.d/default.conf` – Nginx vhost for Laravel

Start with `docker-compose up -d --build`; stop with `docker-compose down`.

## Licence

MIT – feel free to fork and extend.

---
