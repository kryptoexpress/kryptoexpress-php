<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Rules;

use KryptoExpress\SDK\Contracts\FiatConverterInterface;
use KryptoExpress\SDK\Error\MinimumAmountError;

final class MinimumAmountPolicy
{
    public function __construct(
        ?FiatConverterInterface $converter = null,
        private readonly float $minimumUsdEquivalent = 1.0,
    ) {
        unset($converter);
    }

    public function assertSatisfied(float $fiatAmount, string $fiatCurrency): void
    {
        if (strtoupper($fiatCurrency) !== 'USD') {
            return;
        }

        if ($fiatAmount < $this->minimumUsdEquivalent) {
            throw new MinimumAmountError(sprintf(
                'The minimum payment amount for USD payments is %.2f USD.',
                $this->minimumUsdEquivalent,
            ));
        }
    }
}
