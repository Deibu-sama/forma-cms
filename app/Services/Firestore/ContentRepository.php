<?php

namespace App\Services\Firestore;

use Google\Cloud\Firestore\FirestoreClient;
use Throwable;

/**
 * Read-only public content adapter.
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
            'site_name' => config('app.name', 'FormaCMS'),
            'tagline' => 'Your work. Your story. Your style.',
            'eyebrow' => 'Independent creative professional',
            'headline' => 'Thoughtful work. Made with purpose.',
            'description' => 'I turn ideas into meaningful experiences through considered design, clear strategy, and a little curiosity.',
            'about_headline' => 'Good work starts with listening, then making something that matters.',
            'about_text' => 'I partner with people and teams to turn ambitious ideas into clear, considered outcomes. My approach is collaborative, thoughtful, and always focused on the people on the other side of the work.',
            'contact_headline' => 'Let’s make something meaningful.',
            'contact_text' => 'Have a project, a question, or an idea you want to explore? I’d love to hear about it.',
            'theme' => 'classic-corporate',
            'contact_email' => '',
        ];
    }

    public function publishedProjects(): array
    {
        $client = $this->client();

        if (! $client) {
            return [
                [
                    'title' => 'Brand identity concept',
                    'summary' => 'A sample case study showing how a clear visual system can bring a brand to life.',
                    'category' => 'Branding',
                    'art_label' => 'Brand identity',
                    'image_url' => '',
                    'image_alt' => 'Abstract brand identity concept artwork',
                ],
                [
                    'title' => 'Digital experience concept',
                    'summary' => 'A sample project focused on creating a more intuitive, welcoming digital experience.',
                    'category' => 'Digital',
                    'art_label' => 'Digital experience',
                    'image_url' => '',
                    'image_alt' => 'Abstract digital experience concept artwork',
                ],
                [
                    'title' => 'Editorial direction concept',
                    'summary' => 'A sample exploration of typography, layout, and a distinctive editorial voice.',
                    'category' => 'Art direction',
                    'art_label' => 'Editorial direction',
                    'image_url' => '',
                    'image_alt' => 'Abstract editorial direction concept artwork',
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
