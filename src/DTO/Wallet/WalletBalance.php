<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\DTO\Wallet;

final class WalletBalance
{
    /**
     * @param array<string, float> $balances
     */
    public function __construct(public readonly array $balances)
    {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $balances = [];

        foreach ($payload as $currency => $amount) {
            if (!is_string($currency) || (!is_int($amount) && !is_float($amount))) {
                continue;
            }

            $balances[$currency] = (float) $amount;
        }

        return new self($balances);
    }

    public function amountFor(string $cryptoCurrency): float
    {
        return $this->balances[$cryptoCurrency] ?? 0.0;
    }
}
