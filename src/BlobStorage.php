<?php

declare(strict_types=1);

final class BlobStorage
{
    /**
     * @var callable(string, array<string>, string): int
     */
    private $request;

    /**
     * @param callable(string, array<string>, string): int|null $request
     */
    public function __construct(?callable $request = null)
    {
        $this->request = $request ?? $this->sendRequest(...);
    }

    public function delete(string $url): void
    {
        if (!$this->isManagedBlobUrl($url)) {
            return;
        }

        $token = getenv('DYNAMIC_GALERY_READ_WRITE_TOKEN')
            ?: getenv('BLOB_READ_WRITE_TOKEN')
            ?: '';

        if ($token === '') {
            throw new RuntimeException('Falta el token de lectura y escritura de Vercel Blob.');
        }

        $storeId = $this->storeIdFromEnvironment() ?: $this->storeIdFromToken($token);
        $apiUrl = rtrim((string) (getenv('VERCEL_BLOB_API_URL') ?: 'https://vercel.com/api/blob'), '/') . '/delete';
        $payload = json_encode(['urls' => [$url]], JSON_THROW_ON_ERROR);
        $requestId = bin2hex(random_bytes(16));
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'x-api-blob-request-id: ' . $requestId,
            'x-api-blob-request-attempt: 0',
            'x-api-version: 12',
            'x-vercel-blob-store-id: ' . $storeId,
        ];
        $status = ($this->request)($apiUrl, $headers, $payload);

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException('Vercel Blob rechazo la eliminacion (HTTP ' . $status . ').');
        }
    }

    public function isManagedBlobUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && is_string($host)
            && (bool) preg_match('/^[a-z0-9-]+\.(public|private)\.blob\.vercel-storage\.com$/i', $host);
    }

    private function storeIdFromToken(string $token): string
    {
        $parts = explode('_', $token);
        $storeId = $parts[3] ?? '';

        if ($storeId === '') {
            throw new RuntimeException('El token de Vercel Blob no contiene un identificador de store valido.');
        }

        return $storeId;
    }

    private function storeIdFromEnvironment(): string
    {
        $storeId = (string) (getenv('DYNAMIC_GALERY_STORE_ID') ?: getenv('BLOB_STORE_ID') ?: '');

        return str_starts_with($storeId, 'store_')
            ? substr($storeId, strlen('store_'))
            : $storeId;
    }

    /**
     * @param array<string> $headers
     */
    private function sendRequest(string $url, array $headers, string $payload): int
    {
        $handle = curl_init($url);

        if ($handle === false) {
            throw new RuntimeException('No se pudo iniciar la solicitud de eliminacion en Vercel Blob.');
        }

        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($handle);
        $curlError = curl_error($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        if ($response === false || $curlError !== '') {
            throw new RuntimeException('Vercel Blob no respondio al eliminar la imagen: ' . $curlError);
        }

        return $status;
    }
}
