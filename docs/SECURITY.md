# Security Model

## Trust boundaries

- Browser visitors can read only public, published website content through Laravel.
- Admin routes require a Laravel session and an explicit authorization ability.
- Laravel accesses Firestore using server-side credentials.
- Inquiry records and admin data are private.
- External image URLs are displayed by the browser; the application does not fetch arbitrary URLs on the server.

## Firestore rules

If the browser never accesses Firestore directly, use deny-by-default client rules:

```javascript
rules_version = '2';

service cloud.firestore {
  match /databases/{database}/documents {
    match /{document=**} {
      allow read, write: if false;
    }
  }
}
```

Server Firestore SDKs bypass Firestore Security Rules and are authorized through Google Cloud IAM. Therefore this ruleset does not replace IAM or Laravel authorization.

## IAM and credentials

- Use a dedicated service account.
- Grant only required Firestore permissions.
- Prefer workload identity / Application Default Credentials supported by the host.
- Never commit service-account JSON or `.env`.
- Do not expose credentials to browser JavaScript.
- Rotate/revoke credentials if they are accidentally exposed.

## Laravel application controls

- Use maintained Laravel releases and update dependencies.
- Enable CSRF protection on state-changing web requests.
- Use secure, HTTP-only, SameSite session cookies and HTTPS in production.
- Rate-limit login and contact endpoints.
- Require authentication and authorization on every admin endpoint.
- Disable public administrator registration; provision initial administrators using the server-only `bookkeeper:make-admin` Artisan command.
- Require the `is_admin` flag and session authentication for every `/admin` route.
- Validate requests with Form Requests and explicit field allowlists.
- Escape output; do not render user-supplied HTML.
- Do not trust hidden fields for ownership or permissions.
- Keep production debug output disabled.
- Log operational metadata without secrets or full inquiry messages.

## External image URL policy

- Require HTTPS.
- Reject URLs containing embedded credentials.
- Enforce a reasonable maximum URL length.
- Consider an allowlist of image hosts.
- Do not server-fetch submitted URLs in v1.
- If server-side image inspection is added later, mitigate SSRF with DNS/IP checks, redirect controls, response-size limits, and a strict host policy.
- Restrict Content Security Policy `img-src` to the site origin and approved hosts where practical.

## Contact form and privacy

- Validate and bound every field.
- Add rate limiting and honeypot/CAPTCHA protection as appropriate.
- The current Laravel endpoint stores validated enquiries in Firestore when configured. Email notification dispatch is not implemented; if added, persist first.
- Keep inquiry reads private and paginated.
- If using `site_settings_fallback`, secure the SQL database and use persistent managed storage; it contains public website settings but must not be made directly accessible to visitors.
- Define purpose, retention period, deletion workflow, and privacy notice before production.
- Avoid collecting tax IDs, bank credentials, or client financial files through a public contact form.

## Release checklist

- [ ] Secrets and local files are excluded from Git.
- [ ] IAM permissions are least-privilege.
- [ ] Admin routes are authorization-tested.
- [ ] Public queries exclude drafts and inquiries.
- [ ] Image URL handling is tested.
- [ ] Rate limiting and CSRF are enabled.
- [ ] HTTPS and secure cookies are configured.
- [ ] Backups and restoration are tested.
