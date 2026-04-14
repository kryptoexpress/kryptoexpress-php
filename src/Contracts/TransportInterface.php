<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Contracts;

interface TransportInterface
{
    /**
     * @param array<string, scalar|null> $query
     * @param array<string, mixed>|null $json
     * @return array<string, mixed>
     */
    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $json = null,
        bool $authenticated = true,
    ): array;
}
