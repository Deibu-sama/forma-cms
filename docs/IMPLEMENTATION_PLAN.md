# FormaCMS — Implementation Plan

## 1. Product definition

FormaCMS is a reusable, theme-driven CMS starter for portfolio and service-business websites. It must not assume a particular owner, business, country, service category, or contact address. Branding and content are installation-level configuration.

### Primary users
- Freelancers and consultants
- Portfolio owners
- Small service businesses
- Developers adapting the starter for a client

### Product principles
1. Brand-neutral defaults and seed content.
2. Reusable section types rather than arbitrary HTML.
3. Themes change presentation, not business content.
4. Server-side validation and authorization.
5. Simple deployment and documented extension points.

## 2. Architecture

- Laravel serves public pages and admin UI using Blade.
- Tailwind CSS and Vite build the frontend assets.
- Firestore stores site settings, pages/sections, services, projects, packages, FAQs, and inquiries.
- Laravel authentication uses a SQL-backed users/session store.
- Laravel is the only layer that accesses Firestore using server credentials.
- Laravel Mail and a queue deliver inquiry notifications.
- External image URLs are stored as metadata; uploaded files are out of scope for v1.

## 3. Public pages

- Home
- About
- Services
- Optional industry or compliance information page
- Portfolio index and project detail
- Packages
- FAQs
- Contact
- Privacy notice

Page presence and navigation visibility should be configurable. Do not assume tax, accounting, or other regulated services are available in every installation.

## 4. Admin features

### Dashboard
Show counts of published projects, drafts, and new inquiries without exposing inquiry data to public routes.

### Content
- Create, edit, publish, unpublish, and delete pages/sections.
- Support a fixed allowlist of section types.
- Reorder sections and toggle visibility.
- Validate fields by section type.
- Preview draft content without publishing it.

### Theme editor
- Select one of three built-in presets.
- Configure primary, background, surface, accent, text, and muted colors.
- Configure supported font and radius options.
- Preview before publishing.
- Validate all theme fields and use safe defaults.

### Portfolio
- Title, slug, summary, body, category, software/tools, image URL, alt text, order, status.
- External HTTPS image URLs only in v1.
- Never server-fetch arbitrary image URLs.
- Include a demonstration-data flag and disclosure.

### Inquiries
- Store validated submissions privately.
- Track new, contacted, in-discussion, and closed statuses.
- Paginate and authorize all admin access.
- Queue notification email after a successful database write.

## 5. Data model

See [FIRESTORE_SCHEMA.md](FIRESTORE_SCHEMA.md). Keep documents small and collections separate. Do not place the entire site in one document.

## 6. Suggested Laravel structure

```text
app/
  Http/Controllers/
    SiteController.php
    ContactController.php
    Admin/
  Http/Requests/
  Policies/
  Services/
    Firestore/
    ThemeService.php
    ImageUrlValidator.php
resources/views/
  layouts/
  components/
  pages/
  admin/
routes/
  web.php
config/services.php
docs/
```

Use controllers for HTTP orchestration, Form Requests for validation, policies/Gates for authorization, and repositories/services for Firestore access. Avoid passing request payloads directly to Firestore.

## 7. Admin authentication

- Use supported Laravel authentication scaffolding.
- No public administrator registration.
- Create the first administrator through a restricted setup process.
- Hash passwords with Laravel's configured hasher.
- Regenerate sessions after login and invalidate them on logout.
- Rate-limit login attempts.
- Protect all management routes with authentication and an explicit ability such as `manage-site`.
- Keep users and sessions in SQL for compatibility with Laravel's conventional auth stack.

## 8. Theme implementation

Use static Tailwind utility classes and CSS custom properties for runtime colors. Do not construct Tailwind class names from arbitrary database values. Whitelist theme IDs and supported layout options. Validate colors against a strict format and never accept arbitrary CSS, HTML, JavaScript, or URL expressions in theme settings.

## 9. Contact workflow

1. Validate request fields and apply rate limits/spam protection.
2. Persist inquiry using a server-generated document ID and timestamp.
3. Dispatch a notification job after persistence.
4. Show a generic success message.
5. Retry email delivery without duplicating the inquiry.
6. Track notification status without logging message contents.

## 10. Non-functional requirements

- Responsive mobile, tablet, and desktop layouts.
- Accessible keyboard navigation, focus states, and alt text.
- Safe output escaping and CSRF protection.
- No secrets in source control.
- Published-only public queries.
- Pagination for portfolio and inquiries.
- Documented backup/restore procedure.
- SEO metadata and sitemap.
- Privacy notice and defined inquiry retention period.

## 11. Hosting decision

Select hosting only after verifying PHP version, Firestore client requirements (including gRPC where required), outbound Google API connectivity, persistent sessions, queue workers, scheduled tasks, and secret management. A managed PHP host may be simplest if compatible; a VPS offers more control but requires server maintenance.

## 12. Definition of done

A release is ready when all roadmap acceptance criteria pass, admin routes are protected, published content is the only public content, inquiries are private, theme settings are validated, image URLs are handled safely, and deployment/restore steps have been tested.
