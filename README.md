# FormaCMS

**A theme-driven, customizable portfolio CMS starter built with Laravel, Tailwind CSS, and Firebase Cloud Firestore.**

FormaCMS is an open-source foundation for personal portfolios, freelancers, consultants, and small-business websites. It is designed to be configured for different brands rather than hardcoded for a single person or business.

> Project status: initial foundation and implementation plan. Review the setup guide before deploying. This repository is a starter kit, not a production-audited CMS.

## Goals

- Three reusable visual themes and configurable design tokens.
- Editable page sections with ordering and visibility controls.
- Portfolio projects with external HTTPS image URLs.
- Editable services, packages, FAQs, navigation, and contact settings.
- Laravel-managed admin authentication.
- Firestore-backed public content and inquiries.
- Responsive Blade views styled with Tailwind CSS.
- Clear security, privacy, deployment, and contribution guidance.

## Planned stack

- PHP and Laravel (choose a supported stable version compatible with your host)
- Blade templates and Tailwind CSS
- Vite for frontend assets
- Google Cloud Firestore PHP client
- Laravel authentication and a SQL-backed user/session store
- Laravel Mail and a queue backend for reliable inquiry notifications

### Why SQL for admin users?

Laravel's conventional authentication/session stack is simpler and better supported with a relational database. FormaCMS uses Firestore for website content and inquiries; a small SQL database can hold administrator accounts and sessions. Do not store plaintext passwords or expose Firestore service-account credentials to the browser.

## Documentation

- [Implementation plan](docs/IMPLEMENTATION_PLAN.md)
- [Firestore data model](docs/FIRESTORE_SCHEMA.md)
- [Security model](docs/SECURITY.md)
- [Development roadmap](docs/ROADMAP.md)
- [Contributing](CONTRIBUTING.md)
- [Security reporting](SECURITY.md)

## Intended features

### Public website
Home, About, Services, Tax/Compliance Support, Portfolio, Packages, FAQs, and Contact pages, all rendered from editable content.

### Admin dashboard
Manage page sections, theme settings, projects, services, packages, FAQs, site metadata, and inquiries. Management routes must require authenticated and authorized administrators.

### Themes
1. **Classic Corporate** — navy, ivory, muted gold.
2. **Modern Minimal** — light surfaces and a restrained green accent.
3. **Premium Dark** — dark surfaces and warm gold accents.

Theme names and palettes are examples. Site owners can select and configure themes in their own installation.

## Development status

The initial repository establishes the project specification and implementation roadmap. Features should be marked complete only after code is implemented and tested. Consult the roadmap for the planned milestones.

## Security notes

- Never commit `.env`, credentials, service-account JSON, database dumps, or real client data.
- Use server-side Firestore access and least-privilege Google Cloud IAM.
- If browser clients do not access Firestore directly, keep Firestore client rules deny-by-default.
- Validate user input and external image URLs; do not fetch arbitrary URLs from the server.
- Add rate limiting and spam protection to public forms.
- Keep inquiry records private and define a retention policy.
- Review [SECURITY.md](SECURITY.md) before production deployment.

## License

MIT. See [LICENSE](LICENSE).
