<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\DTO\Payment;

use DateTimeImmutable;
use KryptoExpress\SDK\Enum\PaymentType;
use KryptoExpress\SDK\Support\ArrayAccessor;

final class Payment
{
    public function __construct(
        public readonly int $id,
        public readonly PaymentType $paymentType,
        public readonly string $fiatCurrency,
        public readonly ?float $fiatAmount,
        public readonly ?float $cryptoAmount,
        public readonly string $cryptoCurrency,
        public readonly int $expireDatetime,
        public readonly int $createDatetime,
        public readonly ?int $paidAt,
        public readonly ?string $address,
        public readonly bool $isPaid,
        public readonly bool $isWithdrawn,
        public readonly ?string $hash,
        public readonly string $callbackUrl,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            ArrayAccessor::int($payload, 'id'),
            PaymentType::from(ArrayAccessor::string($payload, 'paymentType')),
            ArrayAccessor::string($payload, 'fiatCurrency'),
            ArrayAccessor::nullableFloat($payload, 'fiatAmount'),
            ArrayAccessor::nullableFloat($payload, 'cryptoAmount'),
            ArrayAccessor::string($payload, 'cryptoCurrency'),
            ArrayAccessor::int($payload, 'expireDatetime'),
            ArrayAccessor::int($payload, 'createDatetime'),
            ArrayAccessor::nullableInt($payload, 'paidAt'),
            ArrayAccessor::nullableString($payload, 'address'),
            ArrayAccessor::bool($payload, 'isPaid'),
            ArrayAccessor::bool($payload, 'isWithdrawn'),
            ArrayAccessor::nullableString($payload, 'hash'),
            ArrayAccessor::string($payload, 'callbackUrl'),
        );
    }

    public function createdAt(): DateTimeImmutable
    {
        return (new DateTimeImmutable())->setTimestamp((int) floor($this->createDatetime / 1000));
    }
}
