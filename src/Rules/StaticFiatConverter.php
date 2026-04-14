<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Rules;

use KryptoExpress\SDK\Contracts\FiatConverterInterface;
use KryptoExpress\SDK\Error\CurrencyConversionError;

final class StaticFiatConverter implements FiatConverterInterface
{
    /**
     * @param array<string, float> $usdToFiatRates
     */
    public function __construct(private readonly array $usdToFiatRates)
    {
    }

    public function convertUsdToFiat(float $usdAmount, string $fiatCurrency): float
    {
        $code = strtoupper($fiatCurrency);

        if ($code === 'USD') {
            return $usdAmount;
        }

        $rate = $this->usdToFiatRates[$code] ?? null;

        if ($rate === null) {
            throw new CurrencyConversionError(sprintf('Missing USD -> %s conversion rate.', $code));
        }

        return $usdAmount * $rate;
    }
}
