# API Starter Kit

Laravel 13 **API-only** starter. No views, no Vite, no sessions, no web routes. Errors are always JSON. Token auth with Sanctum, Docker via Sail, and a QA toolchain wired in.

## Stack

| Area | Package |
|---|---|
| Runtime | PHP 8.3+ · Laravel 13 · Sail (MySQL, Redis, Mailpit) |
| Auth | Laravel Sanctum (bearer tokens) |
| API docs | Scribe (dev only) |
| Style | Pint (`laravel` preset + `declare_strict_types`) |
| Refactor | Rector + rector-laravel |
| Static analysis | Larastan, level 8 |
| Tests | Pest 4 (sqlite in-memory) |
| Dev tools | Pail, IDE Helper, Telescope (local env only) |

## Getting started

```sh
git clone <repo> my-api && cd my-api
rm -rf .git
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

API runs at `http://localhost`. Health check: `GET /up`.

> `.env.example` points at Sail hosts (`mysql`, `redis`, `mailpit`). Run artisan through `sail` or change `DB_HOST` / `REDIS_HOST` to `127.0.0.1`.

Optional shell alias: `alias sail='./vendor/bin/sail'`.

## Commands

### Composer scripts

| Command | What it does |
|---|---|
| `composer qa` | Runs everything below in order: lint check, rector dry-run, phpstan, tests. Same as CI. |
| `composer lint` | Pint, fixes code style. |
| `composer lint:check` | Pint in test mode. Fails on style issues, changes nothing. |
| `composer rector` | Rector dry-run. Shows what would change. |
| `composer rector:fix` | Rector applies refactors. Run `composer lint` after. |
| `composer analyse` | Larastan (PHPStan level 8) on `app/`. |
| `composer test` | Pest. |
| `composer docs` | Generates Scribe docs. |
| `composer dev` | `php artisan serve` (without Sail). |
| `composer setup` | install + `.env` + key + migrate. |

### Sail

```sh
sail up -d            # start containers
sail down             # stop
sail artisan migrate  # run artisan
sail composer require x/y
sail test             # run tests in the container
sail mysql            # MySQL shell
sail redis            # Redis shell
sail tinker
sail shell
```

### Artisan

```sh
sail artisan pail                  # tail logs
sail artisan ide-helper:generate   # IDE helper for facades
sail artisan ide-helper:models -N  # model docblocks
sail artisan scribe:generate       # same as composer docs
sail artisan route:list --except-vendor
sail artisan telescope:clear
```

## Endpoints

| Method | Path | Auth | Description |
|---|---|---|---|
| POST | `/api/v1/auth/login` | – | Email + password → bearer token (throttled 5/min) |
| GET | `/api/v1/auth/me` | Bearer | Current user |
| POST | `/api/v1/auth/logout` | Bearer | Revokes current token |
| GET | `/up` | – | Health check |

```sh
curl -X POST localhost/api/v1/auth/login \
  -H 'Accept: application/json' \
  -d email=user@example.com -d password=password

curl localhost/api/v1/auth/me \
  -H 'Accept: application/json' -H 'Authorization: Bearer <token>'
```

## API docs (Scribe)

Scribe is a **dev dependency**. Production installs with `composer install --no-dev` and has no `/docs` route.

```sh
composer docs
```

- Reads routes under `api/*`, FormRequest `rules()` / `bodyParameters()`, controller docblocks (`@group`, `@unauthenticated`, `@response`) and API Resources.
- Writes OpenAPI to `storage/app/private/scribe/openapi.yaml` and a Postman collection beside it. Import either into Postman, Insomnia or a client generator.
- Serves HTML at `/docs` in local env. Generated views and assets are git-ignored.

## Structure

```
app/Http/Controllers/Api/V1   versioned controllers
app/Http/Requests             FormRequests (validation + Scribe params)
app/Http/Resources            API Resources
routes/api.php                all routes, prefixed /api (v1 group inside)
tests/Feature                 Pest feature tests (RefreshDatabase)
```

## Production

- `composer install --no-dev --optimize-autoloader`
- Telescope and Scribe are not installed with `--no-dev`.
- Set `APP_ENV=production`, `APP_DEBUG=false`.

## CI

`.github/workflows/ci.yml` runs `composer qa` on every push and pull request.
