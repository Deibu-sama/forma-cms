# FormaCMS

**A theme-driven, customizable portfolio CMS starter built with Laravel, Tailwind CSS, and Firebase Cloud Firestore.**

FormaCMS is an open-source foundation for portfolio, freelancer, consultant, and small-business websites. It is intended to be configured for different brands, not hardcoded for one person.

## Project status

The repository contains a Laravel application foundation, a read-only Firestore content adapter, a responsive Blade homepage, a standalone UI prototype, and technical documentation. Full admin CRUD, admin login screens, inquiry processing, comprehensive tests, and production deployment automation remain roadmap work. Do not deploy as a production CMS until those milestones are complete.

## Features in the foundation

- Generic branding and sample content.
- Three visual theme tokens: Classic Corporate, Modern Minimal, and Premium Dark.
- Responsive public homepage and portfolio cards.
- Firestore-backed settings/projects with generic demo fallback when Firestore is not configured.
- Standalone interactive UI prototype.
- Firestore data model, security guidance, and implementation roadmap.
- Docker build option for platforms requiring a Dockerfile.

## Planned stack

- PHP and Laravel 12
- Blade templates, Tailwind CSS v4, and Vite
- Google Cloud Firestore PHP client
- Laravel authentication with SQL-backed admin users/sessions
- Laravel Mail and queue-backed inquiry notifications

Laravel's conventional authentication stack is simpler with a relational database. FormaCMS uses Firestore for website content and inquiries, with SQL for admin users and sessions.

## Quick preview

### Standalone prototype
Open [prototype/index.html](prototype/index.html) in a browser, or run:

```bash
php -S 127.0.0.1:8080 -t prototype
```

Visit http://127.0.0.1:8080. The prototype lets you switch themes and edit sample headings locally; it saves only to that browser and does not connect to Laravel or Firestore.

### Deploying on Abasthan

**Create a PHP App, not a Node.js Web Service.** Abasthan has a dedicated PHP runtime that supports Laravel and Composer. If the dashboard logs say `Environment: node 22` or `composer: not found`, the app was created with the wrong runtime. Create a new app from **Create App → PHP App**, connect this repository and the `main` branch, then let Abasthan detect Laravel/PHP. Do not use the Node.js runtime with Laravel's Composer build command.

The Laravel views use Vite, so `public/build/manifest.json` and its assets must exist. This repository includes a GitHub Actions workflow that builds Vite assets with Node.js and commits `public/build` to `main` for PHP-only hosting. Wait for the **Build frontend assets** workflow to complete and for its asset commit to appear before redeploying Abasthan.

Use this build command on Abasthan's managed PHP runtime (no npm required there):

```bash
mkdir -p bootstrap/cache storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs && chmod -R u+rwX bootstrap/cache storage && composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-grpc
```

The public content adapter explicitly selects Firestore's REST transport, which supports the current read-only document/query operations without the native gRPC extension. Do not remove that REST transport setting while deploying on a runtime without `ext-grpc`. If you later need gRPC-only features, deploy with the repository's Dockerfile on a platform that supports Docker and keep the extension installed there.

If the GitHub Actions workflow fails or cannot push its generated assets, check the workflow logs and repository Actions permissions before redeploying.

Set `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `FIRESTORE_PROJECT_ID`, and `FIREBASE_CREDENTIALS_BASE64` in the host environment. `FIREBASE_CREDENTIALS_BASE64` must contain the Base64 encoding of the entire Google Cloud service-account JSON file; Laravel decodes it in memory and passes the credentials to the Firestore client using REST transport. Do not paste raw JSON into this variable, commit the JSON file, or expose either variable to browser code. Create the Base64 value locally and paste the one-line output into Abasthan's secret/environment variable field. After changing environment variables, redeploy or restart the app and run `php artisan optimize:clear`.

**Database warning:** SQLite in ephemeral hosting can lose data on replacement. For production admin accounts and sessions, configure a managed SQL database and set the database environment variables. The current starter does not yet include the full admin dashboard or CMS write operations.

### Docker deployment on hosts that require it

The root [Dockerfile](Dockerfile) installs and enables the native PHP `grpc` extension required by the Firestore SDK, builds Composer dependencies and Tailwind/Vite assets in separate stages, and runs the app on PHP 8.3. Use this on a hosting platform that explicitly supports Dockerfile/container deployments. A managed PHP runtime must separately provide/enable `grpc`; ignoring the Composer platform requirement is not a production fix.

## Documentation

- [Local setup](docs/LOCAL_SETUP.md)
- [Implementation plan](docs/IMPLEMENTATION_PLAN.md)
- [Firestore data model](docs/FIRESTORE_SCHEMA.md)
- [Security model](docs/SECURITY.md)
- [Roadmap and acceptance criteria](docs/ROADMAP.md)
- [Contributing](CONTRIBUTING.md)
- [Security reporting](SECURITY.md)

## Security basics

- Never commit `.env`, service-account JSON, credentials, real client data, or database dumps.
- Keep privileged Firestore access on the Laravel server.
- Use least-privilege Google Cloud IAM.
- If browsers do not access Firestore directly, deny client access in Firestore Security Rules.
- Validate form input and external image URLs; do not fetch arbitrary URLs from the server.
- Protect admin routes with authentication and authorization before adding CMS write operations.
- Add rate limiting and spam protection to public forms.
- Define inquiry retention and privacy practices before production.

## License

MIT. See [LICENSE](LICENSE).
