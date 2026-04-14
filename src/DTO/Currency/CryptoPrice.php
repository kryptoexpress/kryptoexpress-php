<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\DTO\Currency;

use KryptoExpress\SDK\Support\ArrayAccessor;

final class CryptoPrice
{
    public function __construct(
        public readonly string $cryptoCurrency,
        public readonly string $fiatCurrency,
        public readonly float $price,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            ArrayAccessor::string($payload, 'cryptoCurrency'),
            ArrayAccessor::string($payload, 'fiatCurrency'),
            ArrayAccessor::nullableFloat($payload, 'price') ?? 0.0,
        );
    }
}
