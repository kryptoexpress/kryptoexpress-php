<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Tests\Unit\Webhook;

use KryptoExpress\SDK\Error\ValidationError;
use KryptoExpress\SDK\Webhook\SignatureVerifier;
use PHPUnit\Framework\TestCase;

final class SignatureVerifierTest extends TestCase
{
    public function testVerifiesCompactJsonSignature(): void
    {
        $rawJson = "{\n  \"status\": \"paid\",\n  \"amount\": 1.0\n}";
        $secret = 'callback-secret';
        $signature = hash_hmac('sha512', '{"status":"paid","amount":1.0}', $secret);

        self::assertTrue((new SignatureVerifier())->verify($rawJson, $secret, $signature));
    }

    public function testAssertValidThrowsOnInvalidSignature(): void
    {
        $this->expectException(ValidationError::class);

        (new SignatureVerifier())->assertValid('{"status":"paid"}', 'secret', 'wrong');
    }
}
