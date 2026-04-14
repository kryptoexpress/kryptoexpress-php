<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\DTO\Payment;

use KryptoExpress\SDK\Enum\PaymentType;

final class CreatePaymentRequest
{
    public function __construct(
        public readonly PaymentType $paymentType,
        public readonly string $cryptoCurrency,
        public readonly string $fiatCurrency,
        public readonly ?float $fiatAmount,
        public readonly string $callbackUrl,
        public readonly ?string $callbackSecret = null,
    ) {
    }

    public static function payment(
        string $cryptoCurrency,
        string $fiatCurrency,
        float $fiatAmount,
        string $callbackUrl,
        ?string $callbackSecret = null,
    ): self {
        return new self(PaymentType::PAYMENT, $cryptoCurrency, $fiatCurrency, $fiatAmount, $callbackUrl, $callbackSecret);
    }

    public static function deposit(
        string $cryptoCurrency,
        string $fiatCurrency,
        string $callbackUrl,
        ?string $callbackSecret = null,
    ): self {
        return new self(PaymentType::DEPOSIT, $cryptoCurrency, $fiatCurrency, null, $callbackUrl, $callbackSecret);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'paymentType' => $this->paymentType->value,
            'cryptoCurrency' => $this->cryptoCurrency,
            'fiatCurrency' => $this->fiatCurrency,
            'fiatAmount' => $this->fiatAmount,
            'callbackUrl' => $this->callbackUrl,
            'callbackSecret' => $this->callbackSecret,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
