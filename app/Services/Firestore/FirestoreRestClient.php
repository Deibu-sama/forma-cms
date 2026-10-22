<?php

namespace App\Services\Firestore;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use RuntimeException;

/**
 * Small server-side Firestore REST API adapter.
 *
 * The official google/cloud-firestore PHP client requires ext-grpc even when
 * its REST transport is selected. This adapter calls the documented HTTPS API
 * directly so managed PHP hosts without ext-grpc can still use Firestore.
 * Credentials and OAuth tokens remain server-side.
 */
final class FirestoreRestClient
{
    private Client $http;
    private ServiceAccountCredentials $credentials;
    private ?string $accessToken = null;
    private int $tokenExpiresAt = 0;

    public function __construct(
        private readonly string $projectId,
        array $serviceAccount,
    ) {
        $this->credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/datastore'],
            $serviceAccount,
        );

        $this->http = new Client([
            'base_uri' => 'https://firestore.googleapis.com/v1/',
            'connect_timeout' => 5,
            'timeout' => 15,
            'http_errors' => false,
        ]);
    }

    public function getDocument(string $collection, string $documentId): ?array
    {
        [$status, $response] = $this->send('GET', $this->documentPath($collection, $documentId));

        if ($status === 404) {
            return null;
        }

        $this->assertSuccess($status, $response);

        return $this->decodeFields($response['fields'] ?? []);
    }

    public function queryPublishedProjects(): array
    {
        $path = sprintf(
            'projects/%s/databases/(default)/documents:runQuery',
            rawurlencode($this->projectId),
        );

        [$status, $response] = $this->send('POST', $path, [
            'structuredQuery' => [
                'from' => [
                    ['collectionId' => 'portfolio_projects'],
                ],
                'where' => [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'status'],
                        'op' => 'EQUAL',
                        'value' => ['stringValue' => 'published'],
                    ],
                ],
                'limit' => 12,
            ],
        ]);

        $this->assertSuccess($status, $response);

        $projects = [];

        foreach ($response as $row) {
            if (! is_array($row) || ! isset($row['document']['fields'])) {
                continue;
            }

            $project = $this->decodeFields($row['document']['fields']);

            if (! is_array($project)) {
                continue;
            }

            $projects[] = $project;
        }

        return $projects;
    }

    public function setDocument(string $collection, string $documentId, array $data): void
    {
        if ($data === []) {
            return;
        }

        $fieldMasks = [];

        foreach (array_keys($data) as $field) {
            $fieldMasks[] = 'updateMask.fieldPaths='.rawurlencode((string) $field);
        }

        $path = $this->documentPath($collection, $documentId).'?'.implode('&', $fieldMasks);

        [$status, $response] = $this->send('PATCH', $path, [
            'fields' => $this->encodeFields($data),
        ]);

        $this->assertSuccess($status, $response);
    }

    public function createDocument(string $collection, array $data): void
    {
        $path = sprintf(
            'projects/%s/databases/(default)/documents/%s',
            rawurlencode($this->projectId),
            rawurlencode($collection),
        );

        [$status, $response] = $this->send('POST', $path, [
            'fields' => $this->encodeFields($data),
        ]);

        $this->assertSuccess($status, $response);
    }

    private function documentPath(string $collection, string $documentId): string
    {
        return sprintf(
            'projects/%s/databases/(default)/documents/%s/%s',
            rawurlencode($this->projectId),
            rawurlencode($collection),
            rawurlencode($documentId),
        );
    }

    /**
     * @return array{0:int,1:array}
     */
    private function send(string $method, string $path, ?array $body = null): array
    {
        $token = $this->bearerToken();
        $options = [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
        ];

        if ($body !== null) {
            $options['json'] = $body;
        }

        $response = $this->http->request($method, $path, $options);
        $raw = (string) $response->getBody();
        $decoded = $raw === '' ? [] : json_decode($raw, true);
        $payload = is_array($decoded) ? $decoded : [];

        return [$response->getStatusCode(), $payload];
    }

    private function bearerToken(): string
    {
        if ($this->accessToken !== null && time() < $this->tokenExpiresAt) {
            return $this->accessToken;
        }

        $token = $this->credentials->fetchAuthToken();
        $accessToken = $token['access_token'] ?? null;

        if (! is_string($accessToken) || $accessToken === '') {
            throw new RuntimeException('Google authentication did not return an access token.');
        }

        $expiresIn = max(120, (int) ($token['expires_in'] ?? 3600));

        $this->accessToken = $accessToken;
        $this->tokenExpiresAt = time() + $expiresIn - 60;

        return $this->accessToken;
    }

    private function assertSuccess(int $status, array $response): void
    {
        if ($status >= 200 && $status < 300) {
            return;
        }

        $message = $response['error']['message'] ?? 'The Firestore REST API returned an unsuccessful response.';

        throw new RuntimeException(sprintf(
            'Firestore REST request failed with HTTP %d: %s',
            $status,
            is_string($message) ? $message : 'Unknown API error',
        ));
    }

    private function encodeFields(array $fields): array
    {
        $encoded = [];

        foreach ($fields as $name => $value) {
            $encoded[(string) $name] = $this->encodeValue($value);
        }

        return $encoded;
    }

    private function encodeValue(mixed $value): array
    {
        if ($value === null) {
            return ['nullValue' => 'NULL_VALUE'];
        }

        if (is_bool($value)) {
            return ['booleanValue' => $value];
        }

        if (is_int($value)) {
            return ['integerValue' => (string) $value];
        }

        if (is_float($value)) {
            return ['doubleValue' => $value];
        }

        if ($value instanceof \DateTimeInterface) {
            return ['timestampValue' => $value->format('Y-m-d\TH:i:s.uP')];
        }

        if (is_string($value)) {
            return ['stringValue' => $value];
        }

        if (is_array($value)) {
            if (array_is_list($value)) {
                return [
                    'arrayValue' => [
                        'values' => array_map(
                            fn (mixed $item): array => $this->encodeValue($item),
                            $value,
                        ),
                    ],
                ];
            }

            return ['mapValue' => ['fields' => $this->encodeFields($value)]];
        }

        throw new RuntimeException('Unsupported value type in Firestore write payload.');
    }

    private function decodeFields(array $fields): array
    {
        $decoded = [];

        foreach ($fields as $name => $value) {
            if (is_array($value)) {
                $decoded[(string) $name] = $this->decodeValue($value);
            }
        }

        return $decoded;
    }

    private function decodeValue(array $value): mixed
    {
        if (array_key_exists('stringValue', $value)) {
            return $value['stringValue'];
        }

        if (array_key_exists('integerValue', $value)) {
            $integer = (string) $value['integerValue'];

            return filter_var($integer, FILTER_VALIDATE_INT) !== false ? (int) $integer : $integer;
        }

        if (array_key_exists('doubleValue', $value)) {
            return (float) $value['doubleValue'];
        }

        if (array_key_exists('booleanValue', $value)) {
            return (bool) $value['booleanValue'];
        }

        if (array_key_exists('nullValue', $value)) {
            return null;
        }

        if (array_key_exists('timestampValue', $value)) {
            return $value['timestampValue'];
        }

        if (array_key_exists('mapValue', $value)) {
            return $this->decodeFields($value['mapValue']['fields'] ?? []);
        }

        if (array_key_exists('arrayValue', $value)) {
            return array_map(
                fn (array $item): mixed => $this->decodeValue($item),
                $value['arrayValue']['values'] ?? [],
            );
        }

        if (array_key_exists('referenceValue', $value)) {
            return $value['referenceValue'];
        }

        if (array_key_exists('geoPointValue', $value)) {
            return $value['geoPointValue'];
        }

        return null;
    }
}
