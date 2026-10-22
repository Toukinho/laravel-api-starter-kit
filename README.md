# API Starter Kit

A Laravel 13 starter for JSON APIs. It ships with no views, no Vite, no sessions and no web routes. Every error comes back as JSON. Sanctum handles token auth, Sail handles Docker, and one command runs the whole QA toolchain.

## Stack

| Area | Package |
|---|---|
| Runtime | PHP 8.3+, Laravel 13, Sail (MySQL, Redis, Mailpit) |
| Auth | Laravel Sanctum (bearer tokens) |
| API docs | Scribe (dev only) |
| Style | Pint (`laravel` preset plus `declare_strict_types`) |
| Refactoring | Rector with rector-laravel |
| Static analysis | Larastan, level 8 |
| Tests | Pest 4 on in-memory sqlite |
| Dev tools | Pail, IDE Helper, Telescope (local only) |

## Getting started

```sh
git clone <repo-url> my-api && cd my-api
rm -rf .git
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

The API listens on `http://localhost`. Check it with `GET /up`.

`.env.example` uses the Sail hostnames `mysql`, `redis` and `mailpit`. Run artisan through `sail`, or point `DB_HOST` and `REDIS_HOST` at `127.0.0.1` if you run the services on your machine.

Add `alias sail='./vendor/bin/sail'` to your shell config to shorten the commands below.

## Commands

### Composer scripts

| Command | What it does |
|---|---|
| `composer qa` | Runs lint check, Rector dry-run, PHPStan and tests in order. CI runs the same thing. |
| `composer lint` | Fixes code style with Pint. |
| `composer lint:check` | Reports style problems and changes nothing. |
| `composer rector` | Shows the refactors Rector would make. |
| `composer rector:fix` | Applies them. Run `composer lint` afterwards. |
| `composer analyse` | Runs Larastan on `app/`. |
| `composer test` | Runs Pest. |
| `composer docs` | Generates the Scribe docs. |
| `composer dev` | Starts `php artisan serve` without Sail. |
| `composer setup` | Installs dependencies, creates `.env`, sets the key and migrates. |

### Sail

```sh
sail up -d            # start containers
sail down             # stop them
sail artisan migrate  # run artisan
sail composer require vendor/package
sail test             # run tests in the container
sail mysql            # MySQL shell
sail redis            # Redis shell
sail tinker
sail shell
```

### Artisan

```sh
sail artisan pail                  # tail logs
sail artisan ide-helper:generate   # facade helpers for your editor
sail artisan ide-helper:models -N  # model docblocks
sail artisan scribe:generate       # same as composer docs
sail artisan route:list --except-vendor
sail artisan telescope:clear
```

## Endpoints

| Method | Path | Auth | Description |
|---|---|---|---|
| POST | `/api/v1/auth/login` | None | Takes email and password, returns a bearer token. Limited to 5 requests per minute. |
| GET | `/api/v1/auth/me` | Bearer | Returns the current user. |
| POST | `/api/v1/auth/logout` | Bearer | Revokes the current token. |
| GET | `/up` | None | Health check. |

```sh
curl -X POST localhost/api/v1/auth/login \
  -H 'Accept: application/json' \
  -d email=user@example.com -d password=password

curl localhost/api/v1/auth/me \
  -H 'Accept: application/json' -H 'Authorization: Bearer <token>'
```

## API docs

Scribe lives in `require-dev`. A production install with `composer install --no-dev` has no Scribe and no `/docs` route.

```sh
composer docs
```

Scribe reads the routes under `api/*`, the `rules()` and `bodyParameters()` of each FormRequest, the controller docblocks (`@group`, `@unauthenticated`, `@response`) and your API Resources.

It writes an OpenAPI file to `storage/app/private/scribe/openapi.yaml` and a Postman collection next to it. Import either one into Postman, Insomnia or a client generator.

The HTML page at `/docs` is available wherever Scribe is installed. Git ignores the generated views and assets, so run `composer docs` after cloning.

## Structure

```
app/Http/Controllers/Api/V1   versioned controllers
app/Http/Requests             FormRequests for validation and Scribe params
app/Http/Resources            API Resources
routes/api.php                every route, prefixed /api with a v1 group inside
tests/Feature                 Pest feature tests (RefreshDatabase)
```

## Production

Install with `composer install --no-dev --optimize-autoloader`. That leaves out Scribe and Telescope. Set `APP_ENV=production` and `APP_DEBUG=false`.

## CI

`.github/workflows/ci.yml` runs `composer qa` on every push and pull request.

## License

MIT. See [LICENSE](LICENSE).
