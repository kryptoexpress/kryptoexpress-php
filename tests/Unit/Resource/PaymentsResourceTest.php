<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Tests\Unit\Resource;

use KryptoExpress\SDK\ClientConfig;
use KryptoExpress\SDK\DTO\Payment\CreatePaymentRequest;
use KryptoExpress\SDK\Enum\PaymentType;
use KryptoExpress\SDK\Error\MinimumAmountError;
use KryptoExpress\SDK\Error\UnsupportedPaymentModeError;
use KryptoExpress\SDK\KryptoExpressClient;
use KryptoExpress\SDK\Tests\Unit\FakeTransport;
use PHPUnit\Framework\TestCase;

final class PaymentsResourceTest extends TestCase
{
    public function testCreatePaymentSerializesRequestAndMapsResponse(): void
    {
        $transport = new FakeTransport([
            'POST /payment' => [
                'id' => 634,
                'paymentType' => 'PAYMENT',
                'fiatCurrency' => 'USD',
                'fiatAmount' => 1.0,
                'cryptoAmount' => 0.000012,
                'cryptoCurrency' => 'BTC',
                'expireDatetime' => 1745241933410,
                'createDatetime' => 1745234733410,
                'paidAt' => null,
                'address' => 'bc1qaddress',
                'isPaid' => false,
                'isWithdrawn' => false,
                'hash' => 'payment-hash',
                'callbackUrl' => 'https://merchant.example/callback',
            ],
        ]);

        $client = new KryptoExpressClient('key', new ClientConfig(), $transport);
        $payment = $client->payments()->createPayment('BTC', 'USD', 1.0, 'https://merchant.example/callback');

        self::assertSame(PaymentType::PAYMENT, $payment->paymentType);
        self::assertSame('payment-hash', $payment->hash);
        self::assertIsArray($transport->requests[0]['json']);
        self::assertArrayHasKey('fiatAmount', $transport->requests[0]['json']);
        self::assertSame(1.0, $transport->requests[0]['json']['fiatAmount']);
    }

    public function testPaymentLookupUsesPublicEndpoint(): void
    {
        $transport = new FakeTransport([
            'GET /payment' => [
                'id' => 635,
                'paymentType' => 'DEPOSIT',
                'fiatCurrency' => 'USD',
                'fiatAmount' => null,
                'cryptoAmount' => null,
                'cryptoCurrency' => 'BTC',
                'expireDatetime' => 1745242017757,
                'createDatetime' => 1745234817757,
                'paidAt' => null,
                'address' => 'bc1qdeposit',
                'isPaid' => false,
                'isWithdrawn' => false,
                'hash' => 'deposit-hash',
                'callbackUrl' => 'https://merchant.example/callback',
            ],
        ]);

        $client = new KryptoExpressClient('key', new ClientConfig(), $transport);
        $payment = $client->payments()->getByHash('deposit-hash');

        self::assertSame(PaymentType::DEPOSIT, $payment->paymentType);
        self::assertFalse($transport->requests[0]['authenticated']);
    }

    public function testStablecoinSupportsOnlyPaymentMode(): void
    {
        $client = new KryptoExpressClient('key', new ClientConfig(), new FakeTransport());

        $this->expectException(UnsupportedPaymentModeError::class);

        $client->payments()->create(CreatePaymentRequest::deposit(
            'USDT_ERC20',
            'USD',
            'https://merchant.example/callback',
        ));
    }

    public function testMinimumAmountPolicyRejectsTooSmallUsdPayments(): void
    {
        $client = new KryptoExpressClient('key', new ClientConfig(), new FakeTransport());

        $this->expectException(MinimumAmountError::class);

        $client->payments()->createPayment('BTC', 'USD', 0.5, 'https://merchant.example/callback');
    }

    public function testPaymentVsDepositRulesRejectDepositWithFiatAmount(): void
    {
        $client = new KryptoExpressClient('key', new ClientConfig(), new FakeTransport());

        $this->expectException(\KryptoExpress\SDK\Error\SDKError::class);

        $client->payments()->create(new CreatePaymentRequest(
            PaymentType::DEPOSIT,
            'BTC',
            'USD',
            10.0,
            'https://merchant.example/callback',
        ));
    }

    public function testNonUsdPaymentBypassesClientSideMinimumValidation(): void
    {
        $transport = new FakeTransport([
            'POST /payment' => [
                'id' => 1,
                'paymentType' => 'PAYMENT',
                'fiatCurrency' => 'EUR',
                'fiatAmount' => 0.5,
                'cryptoAmount' => 0.0001,
                'cryptoCurrency' => 'BTC',
                'expireDatetime' => 1,
                'createDatetime' => 1,
                'paidAt' => null,
                'address' => 'address',
                'isPaid' => false,
                'isWithdrawn' => false,
                'hash' => 'hash',
                'callbackUrl' => 'https://merchant.example/callback',
            ],
        ]);

        $client = new KryptoExpressClient('key', new ClientConfig(), $transport);

        $payment = $client->payments()->createPayment('BTC', 'EUR', 0.5, 'https://merchant.example/callback');

        self::assertSame('EUR', $payment->fiatCurrency);
        self::assertIsArray($transport->requests[0]['json']);
        self::assertArrayHasKey('fiatAmount', $transport->requests[0]['json']);
        self::assertSame(0.5, $transport->requests[0]['json']['fiatAmount']);
    }
}
