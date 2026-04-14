<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\DTO\Wallet;

use KryptoExpress\SDK\Enum\WithdrawType;

final class WithdrawalRequest
{
    public function __construct(
        public readonly WithdrawType $withdrawType,
        public readonly string $cryptoCurrency,
        public readonly string $toAddress,
        public readonly bool $onlyCalculate = false,
        public readonly ?int $paymentId = null,
    ) {
    }

    public static function all(string $cryptoCurrency, string $toAddress, bool $onlyCalculate = false): self
    {
        return new self(WithdrawType::ALL, $cryptoCurrency, $toAddress, $onlyCalculate);
    }

    public static function single(int $paymentId, string $cryptoCurrency, string $toAddress, bool $onlyCalculate = false): self
    {
        return new self(WithdrawType::SINGLE, $cryptoCurrency, $toAddress, $onlyCalculate, $paymentId);
    }

    public function withOnlyCalculate(bool $onlyCalculate): self
    {
        return new self($this->withdrawType, $this->cryptoCurrency, $this->toAddress, $onlyCalculate, $this->paymentId);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'withdrawType' => $this->withdrawType->value,
            'paymentId' => $this->paymentId,
            'cryptoCurrency' => $this->cryptoCurrency,
            'toAddress' => $this->toAddress,
            'onlyCalculate' => $this->onlyCalculate,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
