<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Webhook;

use KryptoExpress\SDK\Error\ValidationError;
use KryptoExpress\SDK\Support\Json;

final class SignatureVerifier
{
    public function verify(string $rawJson, string $secret, ?string $signature): bool
    {
        if ($signature === null || $signature === '') {
            return false;
        }

        $expected = hash_hmac('sha512', Json::compact($rawJson), $secret);

        return hash_equals($expected, $signature);
    }

    public function assertValid(string $rawJson, string $secret, ?string $signature): void
    {
        if (!$this->verify($rawJson, $secret, $signature)) {
            throw new ValidationError('Invalid callback signature.', 422, 'invalid_signature');
        }
    }
}
