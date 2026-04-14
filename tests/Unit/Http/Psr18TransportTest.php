<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Tests\Unit\Http;

use KryptoExpress\SDK\Error\AuthError;
use KryptoExpress\SDK\ClientConfig;
use KryptoExpress\SDK\Error\NotFoundError;
use KryptoExpress\SDK\Error\RateLimitError;
use KryptoExpress\SDK\Http\Psr18Transport;
use KryptoExpress\SDK\Tests\Unit\FakePsr18Client;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class Psr18TransportTest extends TestCase
{
    public function testMapsNotFoundError(): void
    {
        $transport = new Psr18Transport(
            'key',
            new FakePsr18Client(new Response(404, [], '{"error":"not_found","message":"Missing"}')),
            new Psr17Factory(),
            new ClientConfig(),
        );

        $this->expectException(NotFoundError::class);

        $transport->request('GET', '/payment', ['hash' => 'missing'], authenticated: false);
    }

    public function testMapsAuthError(): void
    {
        $transport = new Psr18Transport(
            'key',
            new FakePsr18Client(new Response(401, [], '{"error":"unauthorized","message":"Bad key"}')),
            new Psr17Factory(),
            new ClientConfig(),
        );

        $this->expectException(AuthError::class);

        $transport->request('GET', '/wallet');
    }

    public function testMapsRateLimitError(): void
    {
        $transport = new Psr18Transport(
            'key',
            new FakePsr18Client(new Response(429, [], '{"error":"rate_limited","message":"Slow down","retryAfter":60}')),
            new Psr17Factory(),
            new ClientConfig(),
        );

        $this->expectException(RateLimitError::class);

        $transport->request('GET', '/wallet');
    }
}
