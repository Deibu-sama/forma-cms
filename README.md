# Bookkeeper Pro Portfolio

**A polished bookkeeping portfolio built with Laravel, React, Tailwind CSS, Vite, and Google Cloud Firestore.**

Bookkeeper Pro Portfolio is a responsive professional website for presenting bookkeeping support, showing illustrative work samples, explaining the working process, answering common questions, and receiving enquiries. It uses a navy, ivory, and muted-gold visual system designed to feel calm, clear, and dependable.

## Stack

- **Laravel 12 / PHP 8.2+** — server-side routing, validation, CSRF protection, rate limiting, and Firestore access.
- **React 19** — interactive public interface, mobile navigation, FAQ accordion, and contact form states.
- **Tailwind CSS v4** — design system, responsive layouts, spacing, type scale, color tokens, and component styling.
- **Vite** — frontend development server and production asset bundling.
- **Google Cloud Firestore** — public site settings and published portfolio entries; private enquiry records are written server-side.
- **SQL / Laravel sessions** — reserved for future authenticated administration. A complete admin CMS is not implemented yet.

Laravel remains the backend. React runs in the browser and never receives Firestore service-account credentials. Tailwind is used for UI styling, not server-side business logic.

## Implemented in this starter

- Responsive hero, services, process, portfolio examples, trust/scope information, FAQs, enquiry form, and footer.
- Brand colors default to navy `#14283F`, ivory `#F5F1E8`, and muted gold `#B99A61`.
- React receives an allowlisted initial page-data payload from Laravel.
- Public settings and published projects are read by the Laravel Firestore adapter.
- The contact endpoint validates input, has a honeypot and request rate limit, and attempts to save enquiries to Firestore.
- SEO title/description, favicon URL support, social sharing metadata, reduced-motion support, keyboard focus styling, and mobile navigation.
- A GitHub Actions workflow builds the Vite production assets for PHP-only hosting.

The project uses clearly labelled illustrative bookkeeping workflows in fallback mode. It does not invent client testimonials, credentials, revenue outcomes, or years of experience.

## Current limitations

This is a public portfolio starter, **not yet a full content management system**. Admin login, settings editor, project CRUD, service/FAQ editors, inquiry review screens, automated email notifications, privacy notice, and retention/deletion workflows are not implemented. Enquiries can be saved only when Firestore is configured and the server account has write permission to the `inquiries` collection. Email notifications are not sent by the current endpoint.

The portfolio describes bookkeeping, record organization, reconciliation support, and reporting preparation. It does not promise regulated tax, legal, audit, or investment advice. Define the actual permitted service scope for the operator’s jurisdiction before publishing.

## Local development

Requirements: PHP 8.2+, Composer, Node.js 22 recommended, npm, and a Firestore project if you want remote content and enquiry persistence.

1. Install backend dependencies and create a local environment:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   Configure a writable local SQL database and run migrations if required by the local environment:

   ```bash
   php artisan migrate
   ```

2. Install frontend dependencies and start the app:

   ```bash
   npm install
   npm run dev
   ```

   In a second terminal, run `php artisan serve`, or use the project’s combined development script:

   ```bash
   composer run dev
   ```

3. For a production-like frontend build:

   ```bash
   npm run build
   ```

The development server is used for local work. The production PHP deployment needs the Vite build output at `public/build/manifest.json`.

## Firestore configuration

Set the following values in the Laravel environment, not in React or other browser code:

- `FIRESTORE_PROJECT_ID`
- `FIREBASE_CREDENTIALS_BASE64`

The credentials variable contains a Base64-encoded service-account JSON document. Generate the Base64 value locally and store it in your hosting provider’s secret/environment settings. Never commit the service-account JSON, `.env`, API credentials, or private financial records.

The adapter uses the Firestore REST transport for environments such as managed PHP hosting that do not provide the native gRPC extension.

### Public content

- `site_settings/main` — public brand, page copy, contact email, SEO description, and approved color tokens.
- `portfolio_projects` — public portfolio records with `status = "published"`.
- `inquiries` — private contact enquiries stored by Laravel. Visitors cannot fetch inquiry records through a public route.

Read [docs/FIRESTORE_SCHEMA.md](docs/FIRESTORE_SCHEMA.md) for the suggested fields. Keep Firestore client rules deny-by-default when the browser never accesses Firestore directly. Use least-privilege IAM permissions for the Laravel service account.

## Deploying on Abasthan

Create a **PHP App**, not a Node.js Web Service, for Laravel.

The GitHub Actions workflow `.github/workflows/build-frontend-assets.yml` runs on pushes to `main`, installs npm dependencies, builds React and Tailwind through Vite, and commits `public/build` for a PHP-only hosting environment. Check that workflow succeeds and the generated-asset commit exists before redeploying.

Example managed-PHP build command:

```bash
mkdir -p bootstrap/cache storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chmod -R u+rwX bootstrap/cache storage
composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-grpc
```

Set production environment values including `APP_NAME="Bookkeeper Pro Portfolio"`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY`, `APP_URL`, Firestore settings, and database/session settings appropriate to your host. After changing environment variables, restart or redeploy and run `php artisan optimize:clear`.

**Database warning:** Do not rely on ephemeral SQLite storage for production admin accounts or durable sessions. Configure a managed SQL database before introducing the authenticated admin CMS.

## Project documentation

- [Implementation plan](docs/IMPLEMENTATION_PLAN.md)
- [Firestore data model](docs/FIRESTORE_SCHEMA.md)
- [Security model](docs/SECURITY.md)
- [Roadmap and acceptance criteria](docs/ROADMAP.md)
- [Contributing](CONTRIBUTING.md)
- [Security reporting](SECURITY.md)

## Security basics

- Never commit `.env`, service-account JSON, secrets, real client records, or database dumps.
- Keep privileged Firestore access on Laravel; never place credentials in React.
- Keep inquiry records private; restrict Firestore IAM and do not add public inquiry-read endpoints.
- Validate public submissions, retain CSRF protection, use request throttling, and avoid collecting sensitive financial details through the contact form.
- Confirm privacy, retention, deletion, and service-scope requirements before production launch.

## License

MIT. See [LICENSE](LICENSE).
