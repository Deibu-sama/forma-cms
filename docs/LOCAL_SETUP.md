# Local Setup

## Prerequisites

- PHP version supported by the chosen Laravel 12 release
- Composer
- Node.js and npm
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
9. Start the app: `php artisan serve`.

If Firestore is not configured, the public homepage uses generic demo content. To connect Firestore, set `FIRESTORE_PROJECT_ID` and configure Google Application Default Credentials for the server environment. Never commit a service-account key.

## Docker

A Dockerfile is provided for platforms that don't supply PHP and Composer in their native build environment.

Build and run locally:

```bash
docker build -t formacms .
docker run --rm -p 8080:8080 \
  -e APP_KEY='base64:REPLACE_WITH_A_REAL_GENERATED_KEY' \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  formacms
```

Generate a real key in a local PHP/Laravel environment using `php artisan key:generate --show`; do not use the placeholder above. Set Firestore project ID and credentials separately if you want real Firestore content.

The container runs migrations on startup. SQLite inside the container is ephemeral unless you mount persistent storage. For production, prefer a managed SQL database for administrator accounts and sessions, and configure the DB environment variables.

## Current implementation boundary

The Laravel foundation currently includes a public homepage, read-only content adapter, and theme preview. Admin authentication screens, full CRUD editor, inquiry processing, seed commands, complete test coverage, and production deployment automation remain roadmap work. Do not treat this as a production CMS until those milestones are implemented and tested.
