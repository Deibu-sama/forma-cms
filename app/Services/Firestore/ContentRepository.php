<?php

namespace App\Services\Firestore;

use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Core\Timestamp;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Public content reader and limited inquiry writer.
 *
 * Firestore credentials are provided by the hosting environment as a
 * base64-encoded service-account JSON document. Never store credentials in
 * source control or send them to the browser.
 */
final class ContentRepository
{
    private ?FirestoreClient $client = null;
    private bool $connectionAttempted = false;
    private bool $firestoreUnavailable = false;

    public function isDemoMode(): bool
    {
        return $this->firestoreUnavailable || ! $this->client();
    }

    public function settings(): array
    {
        $document = $this->readDocument('site_settings', 'main');

        if (! $document) {
            $document = $this->readFallbackSettings();
        }

        $defaults = [
            'site_name' => 'Bookkeeper Pro Portfolio',
            'site_title' => 'Bookkeeper Pro Portfolio | Bookkeeping & Reporting Support',
            'favicon_url' => '',
            'brand_name' => 'Bookkeeper Pro',
            'tagline' => 'Bookkeeping support for clearer business decisions.',
            'eyebrow' => 'BOOKKEEPING · RECONCILIATION · REPORTING',
            'headline' => "Clear books.\nConfident decisions.",
            'description' => 'Reliable bookkeeping support that helps you keep records organized, understand what needs attention, and feel more prepared for your next business decision.',
            'about_headline' => 'Careful with your records. Clear about the work.',
            'about_text' => 'A thoughtful, collaborative approach to organizing records, reconciling accounts where agreed, and preparing information for review. Every engagement starts with a clear scope and practical expectations.',
            'contact_headline' => 'Ready for a clearer bookkeeping routine?',
            'contact_text' => 'Share where things stand today and what kind of bookkeeping support would help. The first step is a conversation about the scope, your workflow, and the information you need.',
            'meta_description' => 'Bookkeeping, reconciliations, and reporting support for a clearer view of your business.',
            'theme' => 'classic-corporate',
            'custom_colors' => [
                'primary' => '#14283F',
                'background' => '#F5F1E8',
                'accent' => '#B99A61',
                'surface' => '#FFFDF8',
                'text' => '#192C41',
                'muted' => '#68727B',
            ],
            'contact_email' => '',
            'hero_image_url' => '',
            'hero_image_alt' => '',
            'availability' => '',
            'work_intro' => '',
            'footer_note' => '',
        ];

        // Only explicitly approved public fields cross the Laravel/React boundary.
        $settings = array_replace($defaults, array_intersect_key($document ?? [], $defaults));
        $providedColors = is_array($settings['custom_colors']) ? $settings['custom_colors'] : [];
        $settings['custom_colors'] = $defaults['custom_colors'];

        foreach ($defaults['custom_colors'] as $key => $fallbackColor) {
            $candidate = $providedColors[$key] ?? null;

            if (is_string($candidate) && preg_match('/^#[0-9a-fA-F]{6}$/', $candidate)) {
                $settings['custom_colors'][$key] = strtoupper($candidate);
            }
        }

        foreach (['favicon_url', 'hero_image_url'] as $urlKey) {
            if (! $this->isSafeHttpsUrl($settings[$urlKey])) {
                $settings[$urlKey] = '';
            }
        }

        if (! is_string($settings['contact_email']) || ! filter_var($settings['contact_email'], FILTER_VALIDATE_EMAIL)) {
            $settings['contact_email'] = '';
        }

        return $settings;
    }

    /**
     * Persist website settings from the authenticated administration area.
     * The caller validates request fields; this service applies its own allowlist.
     */
    /**
     * Persist validated website settings to Firestore, falling back to SQL if
     * Firestore is unavailable. Never accepts arbitrary document fields.
     *
     * @return 'firestore'|'local'|false
     */
    public function saveSettings(array $input): string|false
    {
        $stringFields = [
            'site_name', 'site_title', 'brand_name', 'favicon_url',
            'hero_image_url', 'hero_image_alt', 'eyebrow', 'headline',
            'description', 'about_headline', 'about_text',
            'contact_headline', 'contact_text', 'contact_email',
            'availability', 'work_intro', 'footer_note', 'meta_description',
        ];

        $payload = [];

        foreach ($stringFields as $field) {
            $value = $input[$field] ?? '';

            if (! is_string($value)) {
                return false;
            }

            $payload[$field] = trim($value);
        }

        foreach (['favicon_url', 'hero_image_url'] as $urlField) {
            if ($payload[$urlField] !== '' && ! $this->isSafeHttpsUrl($payload[$urlField])) {
                return false;
            }
        }

        if ($payload['contact_email'] !== '' && ! filter_var($payload['contact_email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $colors = $input['custom_colors'] ?? null;

        if (! is_array($colors)) {
            return false;
        }

        $colorFields = ['primary', 'background', 'accent', 'surface', 'text', 'muted'];
        $safeColors = [];

        foreach ($colorFields as $field) {
            $value = $colors[$field] ?? null;

            if (! is_string($value) || ! preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
                return false;
            }

            $safeColors[$field] = strtoupper($value);
        }

        $payload['custom_colors'] = $safeColors;
        $payload['theme'] = 'classic-corporate';

        $client = $this->client();

        if ($client && ! $this->firestoreUnavailable) {
            try {
                $firestorePayload = $payload;
                $firestorePayload['updated_at'] = new Timestamp(new \\DateTimeImmutable());
                $client->collection('site_settings')->document('main')->set($firestorePayload, ['merge' => true]);

                return 'firestore';
            } catch (Throwable $exception) {
                report($exception);
                $this->firestoreUnavailable = true;
            }
        }

        // Keep the settings form useful in preview mode. Configure Firestore
        // before production if you want content stored in the remote CMS database.
        try {
            $now = now();
            DB::table('site_settings_fallback')->updateOrInsert(
                ['id' => 1],
                [
                    'settings' => json_encode($payload, JSON_THROW_ON_ERROR),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );

            return 'local';
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    public function publishedProjects(): array
    {
        $client = $this->client();

        if (! $client || $this->firestoreUnavailable) {
            return [
                [
                    'title' => 'Monthly bookkeeping setup',
                    'summary' => 'Illustrative workflow for organizing recurring records and defining month-end review checkpoints.',
                    'category' => 'Monthly bookkeeping',
                    'art_label' => 'A repeatable monthly routine',
                    'image_url' => '',
                    'image_alt' => 'Illustrative monthly bookkeeping workflow artwork',
                    'is_demonstration' => true,
                ],
                [
                    'title' => 'Account reconciliation review',
                    'summary' => 'Example checklist for comparing statements, documenting differences, and tracking open questions for review.',
                    'category' => 'Reconciliation',
                    'art_label' => 'Compare. Review. Resolve.',
                    'image_url' => '',
                    'image_alt' => 'Illustrative reconciliation checklist artwork',
                    'is_demonstration' => true,
                ],
                [
                    'title' => 'Reporting pack organization',
                    'summary' => 'Sample approach to arranging supporting schedules and reports for a clear business review.',
                    'category' => 'Reporting',
                    'art_label' => 'Information, organized',
                    'image_url' => '',
                    'image_alt' => 'Illustrative reporting pack artwork',
                    'is_demonstration' => true,
                ],
            ];
        }

        try {
            $documents = $client->collection('portfolio_projects')
                ->where('status', '=', 'published')
                ->orderBy('sort_order')
                ->limit(12)
                ->documents();

            $allowedFields = array_flip([
                'title', 'slug', 'category', 'summary', 'description',
                'image_url', 'image_alt', 'art_label', 'project_url',
                'is_demonstration',
            ]);
            $stringFields = [
                'title', 'slug', 'category', 'summary', 'description',
                'image_url', 'image_alt', 'art_label', 'project_url',
            ];
            $projects = [];

            foreach ($documents as $document) {
                if (! $document->exists()) {
                    continue;
                }

                // Never expose internal fields from the source document to the browser.
                $project = array_intersect_key($document->data(), $allowedFields);

                foreach ($stringFields as $field) {
                    if (isset($project[$field]) && ! is_string($project[$field])) {
                        unset($project[$field]);
                    }
                }

                if (! $this->isSafeHttpsUrl($project['image_url'] ?? null)) {
                    $project['image_url'] = '';
                }

                if (isset($project['project_url']) && ! $this->isSafeHttpsUrl($project['project_url'])) {
                    unset($project['project_url']);
                }

                $projects[] = $project;
            }

            return $projects;
        } catch (Throwable $exception) {
            report($exception);
            $this->firestoreUnavailable = true;

            return [
                [
                    'title' => 'Monthly bookkeeping setup',
                    'summary' => 'Illustrative workflow for organizing recurring records and defining month-end review checkpoints.',
                    'category' => 'Monthly bookkeeping',
                    'art_label' => 'A repeatable monthly routine',
                    'image_url' => '',
                    'image_alt' => 'Illustrative monthly bookkeeping workflow artwork',
                    'is_demonstration' => true,
                ],
                [
                    'title' => 'Account reconciliation review',
                    'summary' => 'Example checklist for comparing statements, documenting differences, and tracking open questions for review.',
                    'category' => 'Reconciliation',
                    'art_label' => 'Compare. Review. Resolve.',
                    'image_url' => '',
                    'image_alt' => 'Illustrative reconciliation checklist artwork',
                    'is_demonstration' => true,
                ],
                [
                    'title' => 'Reporting pack organization',
                    'summary' => 'Sample approach to arranging supporting schedules and reports for a clear business review.',
                    'category' => 'Reporting',
                    'art_label' => 'Information, organized',
                    'image_url' => '',
                    'image_alt' => 'Illustrative reporting pack artwork',
                    'is_demonstration' => true,
                ],
            ];
        }
    }

    /**
     * Store a validated public enquiry in Firestore.
     *
     * Returns false when Firestore is not available; callers must not imply
     * an enquiry was saved unless this method returns true.
     */
    public function createInquiry(array $data): bool
    {
        $client = $this->client();

        if (! $client) {
            return false;
        }

        try {
            $now = new Timestamp(new \DateTimeImmutable());

            $client->collection('inquiries')->add([
                'full_name' => trim((string) ($data['full_name'] ?? '')),
                'email' => strtolower(trim((string) ($data['email'] ?? ''))),
                'business_name' => trim((string) ($data['business_name'] ?? '')),
                'country' => trim((string) ($data['country'] ?? '')),
                'service_required' => trim((string) ($data['service_required'] ?? '')),
                'accounting_software' => trim((string) ($data['accounting_software'] ?? '')),
                'monthly_transaction_volume' => trim((string) ($data['monthly_transaction_volume'] ?? '')),
                'message' => trim((string) ($data['message'] ?? '')),
                'status' => 'new',
                'notification_status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return true;
        } catch (Throwable $exception) {
            report($exception);
            $this->firestoreUnavailable = true;

            return false;
        }
    }

    private function readFallbackSettings(): ?array
    {
        try {
            $encoded = DB::table('site_settings_fallback')->where('id', 1)->value('settings');

            if (! is_string($encoded) || $encoded === '') {
                return null;
            }

            $settings = json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);

            return is_array($settings) ? $settings : null;
        } catch (Throwable) {
            // An unmigrated database is equivalent to the defaults-only preview.
            return null;
        }
    }

    private function isSafeHttpsUrl(mixed $value): bool
    {
        if (! is_string($value) || $value === '' || strlen($value) > 2048 || ! filter_var($value, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parts = parse_url($value);

        return is_array($parts)
            && strtolower((string) ($parts['scheme'] ?? '')) === 'https'
            && ! isset($parts['user'])
            && ! isset($parts['pass'])
            && isset($parts['host']);
    }

    private function readDocument(string $collection, string $id): ?array
    {
        $client = $this->client();

        if (! $client) {
            return null;
        }

        try {
            $document = $client->collection($collection)->document($id)->snapshot();

            return $document->exists() ? $document->data() : null;
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    private function client(): ?FirestoreClient
    {
        if ($this->connectionAttempted) {
            return $this->client;
        }

        $this->connectionAttempted = true;
        $projectId = config('services.firestore.project_id');
        $encodedCredentials = config('services.firestore.credentials_base64');

        if (! is_string($projectId) || trim($projectId) === '') {
            return null;
        }

        if (! is_string($encodedCredentials) || trim($encodedCredentials) === '') {
            report(new \RuntimeException('Firestore is not configured: FIREBASE_CREDENTIALS_BASE64 is missing.'));
            return null;
        }

        try {
            $credentialsJson = base64_decode($encodedCredentials, true);

            if ($credentialsJson === false || $credentialsJson === '') {
                throw new \UnexpectedValueException('FIREBASE_CREDENTIALS_BASE64 is not valid base64.');
            }

            $credentials = json_decode($credentialsJson, true, 512, JSON_THROW_ON_ERROR);

            if (
                ! is_array($credentials)
                || ($credentials['type'] ?? null) !== 'service_account'
                || ! is_string($credentials['client_email'] ?? null)
                || ! is_string($credentials['private_key'] ?? null)
            ) {
                throw new \UnexpectedValueException('Decoded Firebase credentials are not a valid service-account JSON document.');
            }

            $this->client = new FirestoreClient([
                'projectId' => $projectId,
                'keyFile' => $credentials,
                // Abasthan's managed PHP runtime does not provide ext-grpc.
                // REST transport supports the document/query operations, including inquiry creation.
                'transport' => 'rest',
            ]);
        } catch (Throwable $exception) {
            report($exception);
            $this->client = null;
        }

        return $this->client;
    }
}
