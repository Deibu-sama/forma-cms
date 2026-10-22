# Local Setup

## Prerequisites

- PHP version supported by the chosen Laravel 12 release
- Composer
- Node.js and npm (needed to build Tailwind/Vite assets)
- SQLite or MySQL with the matching PHP PDO extension
- Google Cloud project with Firestore enabled (optional for demo fallback)
- Required Google client extensions/dependencies for Firestore on your platform

## Setup

1. Clone the repository.
2. Install dependencies with `composer install`.
3. Copy `.env.example` to `.env`.
4. Generate the application key: `php artisan key:generate`.
5. Create the local SQLite database file if using SQLite: `touch database/database.sqlite`.
6. Run `php artisan migrate`.
7. Install frontend dependencies: `npm install`.
8. Build assets: `npm run build`.
9. Create the first administrator interactively: `php artisan bookkeeper:make-admin admin@example.com`. Use an email you control; the command prompts for a display name and a hidden password of at least 12 characters, and it refuses to overwrite existing users.
10. Start the app: `php artisan serve`.
11. Visit `/admin/login` to sign in, then use `/admin/settings` to edit the public website.


If Firestore is not configured, the public homepage uses clearly labelled illustrative bookkeeping content. To connect Firestore, set `FIRESTORE_PROJECT_ID` and `FIREBASE_CREDENTIALS_BASE64` in the server environment. The latter must be the Base64 encoding of the full service-account JSON document. Never commit a service-account key or expose the variable to React.

## Abasthan deployment

Use Abasthan's dedicated **PHP App** deployment type for this Laravel repository. Do not create a Node.js Web Service. The log `Environment: node 22` combined with `composer: not found` indicates the wrong runtime selection; the repository cannot install Composer from a Node-only runtime by changing the command.

Abasthan PHP documentation describes Composer support for PHP apps. Use the PHP app flow and select a supported PHP version (the repository currently targets Laravel 12 and PHP 8.2+).

The Laravel Blade shell loads the React app through Vite's `@vite` helper, so compiled assets must be available at `public/build/manifest.json`. For Abasthan's managed PHP runtime, use this build command if npm is available:

```bash
mkdir -p bootstrap/cache storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs && chmod -R u+rwX bootstrap/cache storage && composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-grpc && npm install && npm run build
```

This is intentionally paired with the application's explicit Firestore REST transport in `ContentRepository`. The current content layer reads site settings, queries published projects, and writes validated enquiries; use a Firestore client/transport configuration that supports those document and query operations. Do not remove the explicit `'transport' => 'rest'` setting while deploying without ext-grpc. This is different from blindly ignoring the platform requirement while leaving the client on its default gRPC transport.

If Node/npm is unavailable in the PHP build environment, build the frontend assets in CI and deploy the resulting `public/build` directory with the PHP application. Do not switch back to a Node runtime for the Laravel app. If you need gRPC-only Firestore operations later (for example streaming listeners), use a runtime with ext-grpc installed.

If this Abasthan app does not automatically deploy the latest GitHub `main` branch, run the repository sync script from the deployed Laravel application root:

```bash
curl -fLsS https://raw.githubusercontent.com/Deibu-sama/forma-cms/main/scripts/sync-abasthan.sh -o /tmp/sync-abasthan.sh
bash /tmp/sync-abasthan.sh
```

The script overlays the latest application files and built Vite assets, clears Laravel caches, and applies pending migrations. It deliberately preserves `.env`, `vendor`, `storage`, and the existing database. After it completes, create the administrator if needed with `php artisan bookkeeper:make-admin your-admin-email@example.com`.

Set `APP_NAME="Bookkeeper Pro Portfolio"`, `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `FIRESTORE_PROJECT_ID`, and `FIREBASE_CREDENTIALS_BASE64` as environment variables. Use a managed SQL database or persistent storage for Laravel's SQL-backed session/cache/job tables. Never commit service-account credentials.

## Docker

Use the repository's Dockerfile only on platforms that explicitly support Dockerfile/container deployments. It installs the native PHP `grpc` extension required by `google/cloud-firestore`, enables it for Composer dependency resolution and runtime, builds Vite assets in a Node stage, and runs Laravel on PHP 8.3.

Build and test locally:

```bash
docker build -t formacms .
docker run --rm -p 8080:8080 \
  -e APP_KEY="$(php artisan key:generate --show)" \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  formacms
```

Generate `APP_KEY` from a local PHP/Laravel environment and set it as a secret in the hosting dashboard; do not put a real key in source control. The Docker image's PHP build installs the gRPC extension using PECL. A standard Abasthan PHP App build does not automatically inherit extensions installed in this Dockerfile, so use the Docker deployment type for this setup unless Abasthan separately documents how to enable `grpc` in its managed PHP runtime.

For real Firestore content, set `FIRESTORE_PROJECT_ID` and configure Google Application Default Credentials or a securely mounted service-account file. Never commit credentials. The container runs migrations on startup. SQLite inside the container is ephemeral unless you mount persistent storage. For production, prefer a managed SQL database for administrator accounts and sessions, and configure the DB environment variables.

Generate a real key in a local PHP/Laravel environment using `php artisan key:generate --show`; do not use the placeholder above. Set Firestore project ID and credentials separately if you want real Firestore content.

The container runs migrations on startup. SQLite inside the container is ephemeral unless you mount persistent storage. For production, prefer a managed SQL database for administrator accounts and sessions, and configure the DB environment variables.

## Admin and settings

The starter includes a protected `/admin/login`, `/admin` dashboard, and `/admin/settings` editor. Create administrator accounts on the server with `php artisan bookkeeper:make-admin admin@example.com`; there is no public registration. Run migrations before first use. Website settings are saved to Firestore when available, otherwise to the SQL `site_settings_fallback` table. A persistent production SQL database is required for accounts, sessions, and fallback settings.

Portfolio/service/FAQ CRUD, the enquiry inbox, email notifications, a privacy notice, retention/deletion workflows, complete feature tests, and production staging verification remain outstanding. Do not treat this as a production CMS until those milestones are implemented and tested.
