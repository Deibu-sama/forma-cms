<?php

namespace App\Services\Firestore;

use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Core\Timestamp;
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

    public function isDemoMode(): bool
    {
        return ! $this->client();
    }

    public function settings(): array
    {
        $document = $this->readDocument('site_settings', 'main');

        return $document ?: [
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
        ];
    }

    public function publishedProjects(): array
    {
        $client = $this->client();

        if (! $client) {
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

            $projects = [];
            foreach ($documents as $document) {
                if ($document->exists()) {
                    $projects[] = $document->data();
                }
            }

            return $projects;
        } catch (Throwable $exception) {
            report($exception);

            return [];
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

            return false;
        }
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
                // REST transport supports the read operations used by this adapter.
                'transport' => 'rest',
            ]);
        } catch (Throwable $exception) {
            report($exception);
            $this->client = null;
        }

        return $this->client;
    }
}
