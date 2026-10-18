# FormaCMS

**A theme-driven, customizable portfolio CMS starter built with Laravel, Tailwind CSS, and Firebase Cloud Firestore.**

FormaCMS is an open-source foundation for portfolio, freelancer, consultant, and small-business websites. It is intended to be customized for different brands, not hardcoded for one person.

## Project status

This repository contains the product plan, security and data-model documentation, and a working responsive UI prototype. The prototype demonstrates the visual direction and client-side theme switching; it is not yet a connected Laravel/Firestore CMS. See [Roadmap](docs/ROADMAP.md) for implementation status. Do not deploy the prototype as a production contact or admin system.

## Product goals

- Three built-in themes plus configurable design tokens.
- Editable sections with ordering and visibility controls.
- Portfolio projects with external HTTPS image URLs.
- Editable services, packages, FAQs, navigation, and contact settings.
- Laravel-managed admin authentication.
- Firestore-backed website content and inquiries.
- Responsive Blade views styled with Tailwind CSS.
- Secure-by-default guidance, testing, and deployment docs.

## Planned stack

- PHP and Laravel (select a supported stable release compatible with hosting)
- Blade templates, Tailwind CSS, and Vite
- Google Cloud Firestore PHP client
- Laravel authentication with a SQL-backed admin-user/session store
- Laravel Mail and a queue backend for inquiry notifications

Laravel's conventional authentication stack is simplest with a relational database. Use Firestore for content and inquiry documents, and a small SQL database for admin users and sessions.

## Quick preview

Open [prototype/index.html](prototype/index.html) in a browser, or serve it locally:

```bash
php -S 127.0.0.1:8080 -t prototype
```

Then visit http://127.0.0.1:8080. The prototype includes a theme selector and editable demo text stored in local browser storage. This is a UI preview only; it does not persist to Firestore or provide authentication.

## Documentation

- [Implementation plan](docs/IMPLEMENTATION_PLAN.md)
- [Firestore data model](docs/FIRESTORE_SCHEMA.md)
- [Security model](docs/SECURITY.md)
- [Roadmap and acceptance criteria](docs/ROADMAP.md)
- [Contributing](CONTRIBUTING.md)
- [Security reporting](SECURITY.md)

## Themes

1. **Classic Corporate** — navy, ivory, muted gold.
2. **Modern Minimal** — light surfaces and a restrained green accent.
3. **Premium Dark** — deep charcoal and warm gold.

These are starting presets. Each installation should be able to configure its own brand, content, links, and theme.

## Security basics

- Never commit `.env`, service-account JSON, credentials, real client data, or database dumps.
- Keep privileged Firestore access on the Laravel server.
- Use least-privilege Google Cloud IAM.
- If browsers do not access Firestore directly, deny client access in Firestore Security Rules.
- Validate form input and external image URLs; do not fetch arbitrary URLs from the server.
- Protect admin routes with authentication and authorization.
- Add rate limiting and spam protection to public forms.
- Define inquiry retention and privacy practices before production.

## License

MIT. See [LICENSE](LICENSE).
