<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Http;

use KryptoExpress\SDK\ClientConfig;
use KryptoExpress\SDK\Contracts\TransportInterface;
use KryptoExpress\SDK\Error\SDKError;
use KryptoExpress\SDK\Support\ErrorMapper;
use KryptoExpress\SDK\Support\Json;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class Psr18Transport implements TransportInterface
{
    public function __construct(
        private readonly string $apiKey,
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly ClientConfig $config = new ClientConfig(),
    ) {
    }

    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $json = null,
        bool $authenticated = true,
    ): array {
        $url = rtrim($this->config->baseUrl, '/') . '/' . ltrim($path, '/');

        if ($query !== []) {
            $url .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        $request = $this->requestFactory->createRequest(strtoupper($method), $url)
            ->withHeader('Accept', 'application/json');

        if ($authenticated) {
            $request = $request->withHeader('X-Api-Key', $this->apiKey);
        }

        if ($json !== null) {
            $request->getBody()->write(Json::encode($json));
            $request = $request->withHeader('Content-Type', 'application/json');
        }

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $exception) {
            throw new SDKError('PSR-18 request failed.', 0, $exception);
        }

        $payload = Json::decodeObject((string) $response->getBody());
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 300) {
            return $payload;
        }

        throw ErrorMapper::map($statusCode, $payload);
    }
}
