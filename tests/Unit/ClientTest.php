<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Tests\Unit;

use KryptoExpress\SDK\ClientConfig;
use KryptoExpress\SDK\KryptoExpressClient;
use KryptoExpress\SDK\Rules\StaticFiatConverter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testPsr18TransportSendsApiKeyHeader(): void
    {
        $httpClient = new FakePsr18Client(new Response(200, [], '{"BTC":0.1}'));
        $requestFactory = new Psr17Factory();

        $client = KryptoExpressClient::withPsr18('secret-key', $httpClient, $requestFactory);
        $client->wallet()->get();

        self::assertSame('secret-key', $httpClient->lastRequest?->getHeaderLine('X-Api-Key'));
    }

    public function testClientBuildsResourcesWithInjectedTransport(): void
    {
        $transport = new FakeTransport(['GET /wallet' => ['BTC' => 0.1]]);

        $client = new KryptoExpressClient(
            'secret-key',
            new ClientConfig(fiatConverter: new StaticFiatConverter(['EUR' => 0.91])),
            $transport,
        );

        self::assertSame(0.1, $client->wallet()->get()->amountFor('BTC'));
    }
}
