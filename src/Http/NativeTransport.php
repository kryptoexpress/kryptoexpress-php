<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Http;

use KryptoExpress\SDK\ClientConfig;
use KryptoExpress\SDK\Contracts\TransportInterface;
use KryptoExpress\SDK\Error\APIError;
use KryptoExpress\SDK\Error\SDKError;
use KryptoExpress\SDK\Support\ErrorMapper;
use KryptoExpress\SDK\Support\Json;

final class NativeTransport implements TransportInterface
{
    public function __construct(
        private readonly string $apiKey,
        private readonly ClientConfig $config = new ClientConfig(),
    ) {
    }

    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $json = null,
        bool $authenticated = true
    ): array {
        $attempt = 0;
        $url = $this->buildUrl($path, $query);

        start:
        $headers = ['Accept: application/json'];

        if ($authenticated) {
            $headers[] = 'X-Api-Key: ' . $this->apiKey;
        }

        $content = null;

        if ($json !== null) {
            $headers[] = 'Content-Type: application/json';
            $content = Json::encode($json);
        }

        $context = stream_context_create([
            'http' => [
                'method' => strtoupper($method),
                'header' => implode("\r\n", $headers),
                'content' => $content,
                'ignore_errors' => true,
                'timeout' => $this->config->timeoutSeconds,
            ],
        ]);

        $responseBody = @file_get_contents($url, false, $context);
        $responseHeaders = $http_response_header ?? [];
        $statusCode = $this->extractStatusCode($responseHeaders);

        if ($responseBody === false && $statusCode === 0) {
            throw new SDKError(sprintf('Network request to "%s" failed.', $url));
        }

        $payload = Json::decodeObject($responseBody === false ? '' : $responseBody);

        if ($statusCode >= 200 && $statusCode < 300) {
            return $payload;
        }

        $exception = ErrorMapper::map($statusCode, $payload);

        if ($this->shouldRetry($statusCode, $attempt, $exception)) {
            $attempt++;
            usleep($this->retryDelayMicroseconds($attempt));
            goto start;
        }

        throw $exception;
    }

    /**
     * @param array<string, scalar|null> $query
     */
    private function buildUrl(string $path, array $query): string
    {
        $baseUrl = rtrim($this->config->baseUrl, '/');
        $url = $baseUrl . '/' . ltrim($path, '/');

        if ($query === []) {
            return $url;
        }

        return $url . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * @param list<string> $headers
     */
    private function extractStatusCode(array $headers): int
    {
        foreach ($headers as $header) {
            if (preg_match('/HTTP\/\S+\s+(\d{3})/', $header, $matches) === 1) {
                return (int) $matches[1];
            }
        }

        return 0;
    }

    private function shouldRetry(int $statusCode, int $attempt, APIError $exception): bool
    {
        if ($attempt >= $this->config->maxRetries) {
            return false;
        }

        return $statusCode === 429 || $statusCode >= 500 || $exception->retryAfter !== null;
    }

    private function retryDelayMicroseconds(int $attempt): int
    {
        return (int) ($this->config->retryDelayMilliseconds * (1000 * $attempt));
    }
}
