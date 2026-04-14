<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Rules;

use KryptoExpress\SDK\Contracts\FiatConverterInterface;
use KryptoExpress\SDK\Error\MinimumAmountError;

final class MinimumAmountPolicy
{
    public function __construct(
        private readonly FiatConverterInterface $converter,
        private readonly float $minimumUsdEquivalent = 1.0,
    ) {
    }

    public function assertSatisfied(float $fiatAmount, string $fiatCurrency): void
    {
        $minimumAmount = $this->converter->convertUsdToFiat($this->minimumUsdEquivalent, $fiatCurrency);

        if ($fiatAmount < $minimumAmount) {
            throw new MinimumAmountError(sprintf(
                'The minimum payment amount is equivalent to %.2f USD. Minimum for %s is %.8f.',
                $this->minimumUsdEquivalent,
                strtoupper($fiatCurrency),
                $minimumAmount,
            ));
        }
    }
}
