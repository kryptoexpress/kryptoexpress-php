<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Tests\Unit\Resource;

use KryptoExpress\SDK\ClientConfig;
use KryptoExpress\SDK\KryptoExpressClient;
use KryptoExpress\SDK\Tests\Unit\FakeTransport;
use PHPUnit\Framework\TestCase;

final class CurrenciesResourceTest extends TestCase
{
    public function testGetPricesFormatsQueryAsCommaSeparatedList(): void
    {
        $transport = new FakeTransport([
            'GET /cryptocurrency/price' => [
                ['cryptoCurrency' => 'BTC', 'fiatCurrency' => 'USD', 'price' => 71324],
            ],
        ]);

        $client = new KryptoExpressClient('key', new ClientConfig(), $transport);
        $prices = $client->currencies()->getPrices(['BTC', 'ETH'], 'USD');

        self::assertCount(1, $prices);
        self::assertSame('BTC,ETH', $transport->requests[0]['query']['cryptoCurrency']);
        self::assertFalse($transport->requests[0]['authenticated']);
    }
}
