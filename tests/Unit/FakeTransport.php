<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Tests\Unit;

use KryptoExpress\SDK\Contracts\TransportInterface;

final class FakeTransport implements TransportInterface
{
    /**
     * @var array<int, array{method:string, path:string, query:array<string, scalar|null>, json:array<string, mixed>|null, authenticated:bool}>
     */
    public array $requests = [];

    /**
     * @var array<string, mixed>
     */
    private array $responses = [];

    /**
     * @param array<string, mixed> $responses
     */
    public function __construct(array $responses = [])
    {
        $this->responses = $responses;
    }

    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $json = null,
        bool $authenticated = true
    ): array {
        $this->requests[] = compact('method', 'path', 'query', 'json', 'authenticated');

        $key = strtoupper($method) . ' ' . $path;
        $response = $this->responses[$key] ?? [];

        return is_array($response) ? $response : [];
    }
}
