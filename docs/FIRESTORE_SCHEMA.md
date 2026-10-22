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

```json
{
  "site_name": "Your Name or Business",
  "site_title": "Your Name | Professional Portfolio",
  "favicon_url": "https://example.com/favicon.png",
  "site_tagline": "Your professional tagline",
  "active_theme_id": "classic-corporate",
  "custom_colors": {
    "primary": "#14283F",
    "background": "#F5F1E8",
    "accent": "#B99A61",
    "surface": "#FFFFFF",
    "text": "#14283F",
    "muted": "#526174"
  },
  "contact_email": "",
  "social_links": [],
  "booking_url": "",
  "logo_url": "",
  "default_meta_title": "",
  "default_meta_description": "",
  "updated_at": "Firestore Timestamp"
}
```

Do not seed real personal information into the shared repository. Empty fields are intentional.

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
  "title": "Example Project",
  "slug": "example-project",
  "category": "reporting",
  "summary": "",
  "description": "",
  "image_url": "https://example.com/image.jpg",
  "image_alt": "Describe the image",
  "tools": [],
  "is_demonstration": true,
  "sort_order": 1,
  "status": "draft",
  "updated_at": "Firestore Timestamp"
}
```

Do not server-fetch arbitrary URLs. Basic URL validation is not enough to make server-side fetching safe. Use external HTTPS URLs only for v1.

## packages/{packageId}

Fields: `name`, `slug`, `audience`, `features`, `price` (nullable), `price_label`, `show_price`, `sort_order`, `status`.

## faqs/{faqId}

Fields: `question`, `answer`, `category`, `sort_order`, `status`.

## inquiries/{inquiryId}

```json
{
  "full_name": "Example Person",
  "email": "person@example.com",
  "business_type": "",
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

Example data is synthetic. Validate all fields, minimize collected personal information, restrict admin access, and define a retention policy. Do not log the message body or expose inquiry documents in public queries.

## Consistency and indexing

Firestore has no relational foreign keys or general joins. Resolve related documents through repository methods, use batched writes/transactions where atomicity matters, paginate large lists, and create indexes when Firestore requests them. Keep documents comfortably below the 1 MiB limit.
