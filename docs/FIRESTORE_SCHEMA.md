# Firestore Data Model

Firestore stores documents inside collections. These schemas describe application-level fields; timestamps should be real Firestore timestamp values.

## Collections

| Collection | Purpose | Public read |
|---|---|---|
| `site_settings` | Global branding, active theme, contact links, SEO | Selected safe fields only through Laravel |
| `theme_presets` | Built-in theme definitions | No direct browser access required |
| `pages` | Page metadata and publication status | Published only |
| `page_sections` | Reusable typed content sections | Published and visible only |
| `services` | Service cards and details | Published only |
| `portfolio_projects` | Projects and external image URLs | Published only |
| `packages` | Offerings and optional pricing | Published only |
| `faqs` | Questions and answers | Published only |
| `inquiries` | Contact form submissions | Never public |
| `content_revisions` | Optional audit/revision history | Never public |

## Common content fields

```json
{
  "status": "draft",
  "sort_order": 10,
  "created_at": "Firestore Timestamp",
  "updated_at": "Firestore Timestamp"
}
```

Use explicit IDs for singleton records such as `site_settings/main`. Keep collection documents bounded; avoid storing the entire site in a single document.

## site_settings/main

Only allowlisted public keys from this document are passed to React. Do not put credentials, private client information, or internal notes in public setting fields.

```json
{
  "site_name": "Bookkeeper Pro Portfolio",
  "site_title": "Bookkeeper Pro Portfolio | Bookkeeping & Reporting Support",
  "favicon_url": "https://example.com/favicon.png",
  "brand_name": "Bookkeeper Pro",
  "tagline": "Bookkeeping support for clearer business decisions.",
  "eyebrow": "BOOKKEEPING · RECONCILIATION · REPORTING",
  "headline": "Clear books.\nConfident decisions.",
  "description": "A short, accurate description of the bookkeeping support offered.",
  "about_headline": "Careful with your records. Clear about the work.",
  "about_text": "",
  "contact_headline": "Ready for a clearer bookkeeping routine?",
  "contact_text": "",
  "meta_description": "Bookkeeping, reconciliations, and reporting support for a clearer view of your business.",
  "theme": "classic-corporate",
  "custom_colors": {
    "primary": "#14283F",
    "background": "#F5F1E8",
    "accent": "#B99A61",
    "surface": "#FFFDF8",
    "text": "#192C41",
    "muted": "#68727B"
  },
  "contact_email": "",
  "hero_image_url": "",
  "hero_image_alt": "",
  "availability": "",
  "work_intro": "",
  "footer_note": ""
}
```

The Laravel adapter validates custom colors as six-digit hex values and allows HTTPS URLs only for the favicon and public image fields. Missing setting values fall back to the built-in defaults.

## theme_presets/{themeId}

Fields: `name`, `colors`, `typography`, `style`, `is_builtin`. Use an allowlist of theme IDs, font options, radius values, and layout options.

Suggested presets:
- `classic-corporate`: navy, ivory, gold.
- `modern-minimal`: light surface, muted green accent.
- `premium-dark`: dark surfaces, warm gold.

## pages/{slug}

```json
{
  "title": "Home",
  "slug": "home",
  "meta_title": "",
  "meta_description": "",
  "status": "published",
  "updated_at": "Firestore Timestamp"
}
```

## page_sections/{sectionId}

```json
{
  "page_slug": "home",
  "type": "hero",
  "title": "Your headline",
  "subtitle": "Your supporting statement",
  "body": "",
  "image_url": "",
  "image_alt": "",
  "button_label": "Get in touch",
  "button_url": "/contact",
  "sort_order": 1,
  "is_visible": true,
  "status": "published",
  "updated_at": "Firestore Timestamp"
}
```

Allowed types for v1: `hero`, `rich_text`, `service_cards`, `project_gallery`, `package_cards`, `faq_list`, `process_steps`, and `contact_cta`.

Use `sort_order` on section documents as the canonical ordering source. Validate fields based on `type`; do not render arbitrary HTML from stored content.

## services/{serviceId}

Fields: `country_code` (optional), `title`, `description`, `features`, `sort_order`, `status`, `updated_at`. Keep the model industry-neutral; a site owner defines which services are relevant.

## portfolio_projects/{projectId}

```json
{
  "title": "Monthly bookkeeping setup",
  "slug": "monthly-bookkeeping-setup",
  "category": "Monthly bookkeeping",
  "summary": "An illustrative workflow for organizing recurring records.",
  "description": "",
  "art_label": "A repeatable monthly routine",
  "image_url": "",
  "image_alt": "",
  "project_url": "",
  "tools": [],
  "is_demonstration": true,
  "sort_order": 1,
  "status": "draft",
  "updated_at": "Firestore Timestamp"
}
```

The public reader queries only documents where `status = "published"`, then passes an allowlist of display fields to React. It validates public image/link URLs as HTTPS and excludes internal fields. Do not publish confidential client details, bank statements, account numbers, tax IDs, or unsupported claims about results.

## packages/{packageId}

Fields: `name`, `slug`, `audience`, `features`, `price` (nullable), `price_label`, `show_price`, `sort_order`, `status`.

## faqs/{faqId}

Fields: `question`, `answer`, `category`, `sort_order`, `status`.

## inquiries/{inquiryId}

```json
{
  "full_name": "Example Person",
  "email": "person@example.com",
  "business_name": "",
  "country": "",
  "service_required": "",
  "accounting_software": "",
  "monthly_transaction_volume": "",
  "message": "",
  "status": "new",
  "notification_status": "pending",
  "created_at": "Firestore Timestamp",
  "updated_at": "Firestore Timestamp"
}
```

Laravel validates the fields, applies a per-IP request throttle and checks a honeypot before persisting. The public endpoint does not expose inquiry reads. Email notifications and an admin inquiry-management screen are not implemented yet. Example data is synthetic. Define purpose, retention period, deletion workflow, and a privacy notice before production.

## Consistency and indexing

Firestore has no relational foreign keys or general joins. Resolve related documents through repository methods, use batched writes/transactions where atomicity matters, paginate large lists, and create indexes when Firestore requests them. Keep documents comfortably below the 1 MiB limit.
