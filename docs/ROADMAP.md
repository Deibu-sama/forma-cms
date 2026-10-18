# Roadmap and Acceptance Criteria

This roadmap reflects planned work, not a claim that every feature already exists.

## Milestone 0 — Public project foundation
- [x] Establish generic project identity and scope.
- [x] Add implementation and security documentation.
- [x] Add browser-openable responsive UI prototype.
- [ ] Set up automated checks and contribution workflow.

## Milestone 1 — Laravel application foundation
- [ ] Initialize a supported Laravel application.
- [ ] Configure Tailwind CSS and Vite.
- [ ] Configure Firestore PHP client through a server-side service.
- [ ] Configure SQL-backed admin users and sessions.
- [ ] Add test and local development instructions.

Acceptance: application boots locally; environment configuration is documented; secrets are not committed.

## Milestone 2 — Content layer
- [ ] Implement repositories for settings, pages, sections, services, projects, packages, FAQs, and inquiries.
- [ ] Add validated seed/setup command with generic sample data.
- [ ] Implement published-only public queries.
- [ ] Add pagination and Firestore index documentation.

Acceptance: public pages can render from Firestore documents and drafts remain hidden.

## Milestone 3 — Public website
- [ ] Build shared Blade layout and accessible navigation.
- [ ] Build all configurable page templates.
- [ ] Add metadata, canonical URLs, and sitemap.
- [ ] Add responsive and accessibility checks.

Acceptance: pages render on mobile and desktop, with working links and no hardcoded personal identity.

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
- [ ] Add validated contact form and rate limiting.
- [ ] Store inquiries privately in Firestore.
- [ ] Add queued email notifications and retry status.
- [ ] Build paginated admin inquiry workflow.
- [ ] Add privacy notice and retention policy.

Acceptance: a valid submission is stored once, notification failures can retry, and public users cannot read inquiries.

## Milestone 7 — Production release
- [ ] Verify hosting supports required PHP extensions and outbound Google API access.
- [ ] Configure production secrets and HTTPS.
- [ ] Configure queue worker and scheduler if used.
- [ ] Configure backup/restore and error monitoring.
- [ ] Run dependency, authorization, responsive, accessibility, and SEO checks.

Acceptance: deployment and restore instructions are tested in staging; production debug is disabled.
