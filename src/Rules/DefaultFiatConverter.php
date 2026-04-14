<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Rules;

use KryptoExpress\SDK\Contracts\FiatConverterInterface;
use KryptoExpress\SDK\Error\CurrencyConversionError;

final class DefaultFiatConverter implements FiatConverterInterface
{
    public function convertUsdToFiat(float $usdAmount, string $fiatCurrency): float
    {
        if (strtoupper($fiatCurrency) === 'USD') {
            return $usdAmount;
        }

        throw new CurrencyConversionError(sprintf(
            'Unable to validate the minimum amount in %s without an explicit fiat converter.',
            $fiatCurrency,
        ));
    }
}
