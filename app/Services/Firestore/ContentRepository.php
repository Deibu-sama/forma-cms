<?php

namespace App\Services\Firestore;

use Google\Cloud\Firestore\FirestoreClient;
use Throwable;

/**
 * Read-only public content adapter.
 *
 * If Firestore is not configured, a generic demo dataset keeps local UI
 * development possible. Production deployments should configure Firestore
 * and decide explicitly whether demo fallback is permitted.
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
            'eyebrow' => 'A flexible website foundation',
            'headline' => 'Build a portfolio that feels like you.',
            'description' => 'A customizable portfolio starter for independent professionals and small businesses.',
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
                    'title' => 'Project showcase',
                    'summary' => 'Describe the challenge, your approach, and the result.',
                    'category' => 'Case study',
                    'image_url' => '',
                    'image_alt' => 'Abstract project placeholder',
                ],
                [
                    'title' => 'Service overview',
                    'summary' => 'Explain what you offer and who it helps.',
                    'category' => 'Service',
                    'image_url' => '',
                    'image_alt' => 'Abstract service placeholder',
                ],
                [
                    'title' => 'Featured work',
                    'summary' => 'Present a project with a clear caption and accessible image.',
                    'category' => 'Portfolio',
                    'image_url' => '',
                    'image_alt' => 'Abstract portfolio placeholder',
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

        if (! is_string($projectId) || trim($projectId) === '') {
            return null;
        }

        try {
            $this->client = new FirestoreClient([
                'projectId' => $projectId,
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
