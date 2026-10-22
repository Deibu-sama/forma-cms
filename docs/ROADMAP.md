# Bookkeeper Pro Portfolio — Roadmap and Acceptance Criteria

This roadmap records implemented work in the current branch and what still requires live-host verification or additional product work.

## Phase 0 — Project foundation
- [x] Laravel, React, Tailwind CSS, Vite, and server-side Firestore integration.
- [x] Environment and security documentation.
- [x] Pull-request validation workflow for PHP tests and frontend builds.
- [ ] Confirm branch checks are green before merging.

## Phase 1 — Public experience and navigation
- [x] Responsive public portfolio, FAQ interaction, enquiry form, and reduced-motion support.
- [x] Consistent navy/ivory/gold design tokens.
- [x] Privacy notice page and footer link.
- [x] Public sitemap that excludes admin routes.
- [ ] Verify canonical URL and metadata on the deployed domain.
- [ ] Run automated accessibility checks and manual keyboard/mobile browser review.

## Phase 2 — Admin UX and settings
- [x] Protected admin session login and no public registration.
- [x] Clearer admin navigation labels and enquiry links for desktop and mobile.
- [x] Site identity, contact/footer, color presets, and SEO settings.
- [x] Individual software name/logo URL fields with add/remove controls.
- [x] Server-side HTTPS validation for software logo URLs.
- [x] Initials fallback when a software logo is not configured or fails to load.
- [ ] Browser-test every editor and responsive navigation on the live preview.

## Phase 3 — Content management
- [x] Create/edit/delete portfolio projects.
- [x] Create/edit/delete FAQs and certificates.
- [x] Edit allowlisted portfolio sections and toggle visibility.
- [x] Store website settings in Firestore with a SQL settings fallback.
- [ ] Add explicit draft preview and content ordering controls across all collections.
- [ ] Add pagination and ordering for large project/FAQ/certificate collections.

## Phase 4 — Theme and design system
- [x] Validated built-in theme presets and hex color controls.
- [x] Shared design tokens and static Tailwind classes.
- [ ] Add a true preview-before-publish workflow.
- [ ] Verify contrast ratios for every preset and custom color combination.

## Phase 5 — Enquiry workflow
- [x] Contact form validation, rate limiting, honeypot, and server-side Firestore persistence.
- [x] Protected admin inbox for recent enquiries.
- [x] Admin status transitions: new, contacted, in discussion, and closed.
- [x] Clear disconnected-state messaging; no fabricated fallback enquiries.
- [ ] Add queued notifications, delivery/retry status, and operational alerting.
- [ ] Implement configurable retention/deletion and owner-specific privacy contact details.

## Phase 6 — Security and testing
- [x] Admin routes require authentication and administrator authorization.
- [x] CSRF protection and server-side field validation.
- [x] Feature tests for admin access, software logos, privacy page, and sitemap.
- [x] Pull-request workflow for PHP lint, Laravel feature tests, and Vite build.
- [ ] Confirm the workflow passes on GitHub and address any failures.
- [ ] Test production cookie settings, storage permissions, backups, and Firestore least-privilege IAM.

## Phase 7 — Release and deployment
- [x] Document persistent storage considerations for uploaded images.
- [x] Document Firestore and SQL fallback modes.
- [ ] Run a full smoke test on the actual Render deployment.
- [ ] Confirm environment variables, migrations, asset build, and rollback procedure.
- [ ] Verify HTTPS, production debug settings, mail delivery, and restore process before public launch.

## Definition of done

A release is ready only after pull-request checks pass, deployed admin/public pages are smoke-tested, enquiry access remains private, the privacy notice is customized to actual providers and retention practices, and deployment/rollback steps are verified.