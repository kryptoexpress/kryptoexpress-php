<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Contracts;

interface FiatConverterInterface
{
    public function convertUsdToFiat(float $usdAmount, string $fiatCurrency): float;
}
