# Bookkeeper Pro Portfolio — Roadmap and Acceptance Criteria

This roadmap distinguishes what is implemented from what remains planned. The public React website and inquiry endpoint are present; a full admin CMS and production release process are not complete.

## Milestone 0 — Public project foundation
- [x] Establish generic project identity and scope.
- [x] Add implementation and security documentation.
- [x] Add browser-openable responsive UI prototype.
- [ ] Set up automated checks and contribution workflow.

## Milestone 1 — Laravel + React application foundation
- [x] Establish the Laravel application foundation.
- [x] Configure Tailwind CSS v4, Vite, and React.
- [x] Configure the Firestore PHP client through a server-side service.
- [ ] Configure SQL-backed admin users and sessions.
- [x] Document local development and deployment setup.

Acceptance: production frontend assets build successfully; environment configuration is documented; secrets are not committed.

## Milestone 2 — Content layer
- [x] Read public site settings and published portfolio projects.
- [x] Allowlist public fields before passing Firestore documents to React.
- [x] Store validated inquiries privately through Laravel.
- [ ] Implement repositories for pages, sections, services, packages, and FAQs.
- [ ] Add a validated seed/setup command and CMS data editor.
- [ ] Add pagination and expanded Firestore index documentation.

Acceptance: published project content can render from Firestore and draft records are excluded from the public query.

## Milestone 3 — Public website
- [x] Build the React public portfolio shell and responsive navigation.
- [x] Add service, process, portfolio, FAQ, and enquiry sections.
- [x] Add title, description, favicon support, and social sharing metadata.
- [x] Add mobile menu, FAQ accordion, keyboard focus states, and reduced-motion support.
- [ ] Add canonical URLs, sitemap, automated accessibility checks, and broader page templates.

Acceptance: the homepage builds for production and is responsive. Live-host verification and automated accessibility testing are still required.

## Milestone 4 — Admin CMS
- [ ] Implement Laravel authentication and no-public-registration policy.
- [ ] Add authorization policies/Gates for management routes.
- [ ] Build section editor with allowlisted section types.
- [ ] Add ordering, visibility, draft/publish, and preview.
- [ ] Build CRUD for projects, services, packages, FAQs, and settings.

Acceptance: an authorized admin can update content without editing source code; unauthorized users cannot access admin data.

## Milestone 5 — Theme editor
- [ ] Add classic, minimal, and premium theme presets.
- [ ] Add validated color and style configuration.
- [ ] Use CSS custom properties with static Tailwind classes.
- [ ] Add preview and publish workflow.

Acceptance: theme switching updates shared components consistently without unsafe CSS injection.

## Milestone 6 — Inquiries
- [x] Add a validated React contact form backed by Laravel validation.
- [x] Add request throttling and a honeypot.
- [x] Store inquiries in Firestore on the server when Firestore is configured.
- [ ] Add queued email notifications and retry status.
- [ ] Build a protected, paginated admin inquiry workflow.
- [ ] Publish the privacy notice and define retention/deletion policy.

Acceptance: valid submissions are stored privately when Firestore is available. Email notifications and inquiry-management screens still need implementation.

## Milestone 7 — Production release
- [ ] Verify hosting supports required PHP extensions and outbound Google API access.
- [ ] Configure production secrets and HTTPS.
- [ ] Configure queue worker and scheduler if used.
- [ ] Configure backup/restore and error monitoring.
- [ ] Run dependency, authorization, responsive, accessibility, and SEO checks.

Acceptance: deployment and restore instructions are tested in staging; production debug is disabled.
