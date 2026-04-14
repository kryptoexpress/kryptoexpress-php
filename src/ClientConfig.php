<?php

declare(strict_types=1);

namespace KryptoExpress\SDK;

use KryptoExpress\SDK\Contracts\FiatConverterInterface;

final class ClientConfig
{
    public const DEFAULT_BASE_URL = 'https://kryptoexpress.pro/api';

    public function __construct(
        public readonly string $baseUrl = self::DEFAULT_BASE_URL,
        public readonly float $timeoutSeconds = 10.0,
        public readonly int $maxRetries = 2,
        public readonly int $retryDelayMilliseconds = 250,
        public readonly ?FiatConverterInterface $fiatConverter = null,
        public readonly bool $validateMinimumAmounts = true,
    ) {
    }
}
