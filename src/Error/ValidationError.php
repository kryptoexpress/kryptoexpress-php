<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Error;

final class ValidationError extends APIError
{
    /**
     * @return array<int, array{field:string, message:string}>
     */
    public function fieldErrors(): array
    {
        return $this->details;
    }
}
