<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Tests\Unit\Resource;

use KryptoExpress\SDK\ClientConfig;
use KryptoExpress\SDK\KryptoExpressClient;
use KryptoExpress\SDK\Tests\Unit\FakeTransport;
use PHPUnit\Framework\TestCase;

final class WalletResourceTest extends TestCase
{
    public function testWithdrawalDryRunSetsOnlyCalculateFlag(): void
    {
        $transport = new FakeTransport([
            'POST /wallet/withdrawal' => [
                'id' => 250,
                'withdrawType' => 'ALL',
                'paymentId' => null,
                'cryptoCurrency' => 'LTC',
                'toAddress' => 'ltc1qaddress',
                'txIdList' => [],
                'receivingAmount' => 0.68907986,
                'blockchainFeeAmount' => 0.0000049,
                'serviceFeeAmount' => 0.0,
                'onlyCalculate' => true,
                'totalWithdrawalAmount' => 0.68908476,
                'createDatetime' => 1745237332414,
            ],
        ]);

        $client = new KryptoExpressClient('key', new ClientConfig(), $transport);
        $withdrawal = $client->wallet()->calculateAll('LTC', 'ltc1qaddress');

        self::assertTrue($withdrawal->onlyCalculate);
        self::assertIsArray($transport->requests[0]['json']);
        self::assertArrayHasKey('onlyCalculate', $transport->requests[0]['json']);
        self::assertTrue($transport->requests[0]['json']['onlyCalculate']);
    }
}
