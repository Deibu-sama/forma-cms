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
- Docker build with PHP/Composer and Node.js build stages.

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

### Docker deployment (recommended for hosts without PHP preinstalled)

The included Dockerfile installs Composer dependencies, builds Tailwind assets with Node.js 22, and runs the app using PHP 8.3.

Required runtime environment variables:
- `APP_KEY`: generate with `php artisan key:generate --show` in a local Laravel environment and set the generated value in the host's secret/environment settings.
- `APP_ENV=production`
- `APP_DEBUG=false`
- `FIRESTORE_PROJECT_ID`: Google Cloud project ID if using Firestore.
- Google Application Default Credentials or a securely mounted service-account file if using Firestore.

Set the host's Docker/build mode to use the repository `Dockerfile`. Do not use the Node-only build command or a Node.js start command. The container listens on `$PORT` (defaults to 8080) and runs Laravel migrations on startup.

**Database warning:** SQLite inside a container is ephemeral unless the host provides a persistent disk. For production admin accounts and sessions, configure a managed SQL database and set `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`. If using SQLite, mount persistent storage and point `DB_DATABASE` to that persistent path.

The current starter does not yet include the full admin dashboard or CMS write operations. Review the roadmap before treating it as production-ready.

### Local Laravel application
See [Local setup](docs/LOCAL_SETUP.md) for prerequisites and instructions.

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
