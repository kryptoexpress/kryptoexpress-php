<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Error;

class APIError extends SDKError
{
    /**
     * @param array<int, array{field:string, message:string}> $details
     */
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly ?string $errorCode = null,
        public readonly ?int $retryAfter = null,
        public readonly array $details = [],
    ) {
        parent::__construct($message, $statusCode);
    }
}
