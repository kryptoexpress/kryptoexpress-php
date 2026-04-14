<?php

declare(strict_types=1);

namespace KryptoExpress\SDK;

use KryptoExpress\SDK\Contracts\TransportInterface;
use KryptoExpress\SDK\Http\NativeTransport;
use KryptoExpress\SDK\Http\Psr18Transport;
use KryptoExpress\SDK\Resource\CurrenciesResource;
use KryptoExpress\SDK\Resource\FiatResource;
use KryptoExpress\SDK\Resource\PaymentsResource;
use KryptoExpress\SDK\Resource\WalletResource;
use KryptoExpress\SDK\Rules\DefaultFiatConverter;
use KryptoExpress\SDK\Rules\MinimumAmountPolicy;
use KryptoExpress\SDK\Rules\PaymentRulesValidator;
use KryptoExpress\SDK\Webhook\SignatureVerifier;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class KryptoExpressClient
{
    private readonly TransportInterface $transport;
    private readonly PaymentsResource $payments;
    private readonly WalletResource $wallet;
    private readonly CurrenciesResource $currencies;
    private readonly FiatResource $fiat;
    private readonly SignatureVerifier $signatureVerifier;

    public function __construct(
        private readonly string $apiKey,
        ?ClientConfig $config = null,
        ?TransportInterface $transport = null,
    ) {
        $config ??= new ClientConfig();
        $this->transport = $transport ?? new NativeTransport($apiKey, $config);

        $converter = $config->fiatConverter ?? new DefaultFiatConverter();
        $minimumAmountPolicy = $config->validateMinimumAmounts ? new MinimumAmountPolicy($converter) : null;
        $paymentRulesValidator = new PaymentRulesValidator($minimumAmountPolicy);

        $this->payments = new PaymentsResource($this->transport, $paymentRulesValidator);
        $this->wallet = new WalletResource($this->transport);
        $this->currencies = new CurrenciesResource($this->transport);
        $this->fiat = new FiatResource($this->transport);
        $this->signatureVerifier = new SignatureVerifier();
    }

    public static function withPsr18(
        string $apiKey,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        ?ClientConfig $config = null,
    ): self {
        $config ??= new ClientConfig();

        return new self($apiKey, $config, new Psr18Transport($apiKey, $httpClient, $requestFactory, $config));
    }

    public function payments(): PaymentsResource
    {
        return $this->payments;
    }

    public function wallet(): WalletResource
    {
        return $this->wallet;
    }

    public function currencies(): CurrenciesResource
    {
        return $this->currencies;
    }

    public function fiat(): FiatResource
    {
        return $this->fiat;
    }

    public function webhook(): SignatureVerifier
    {
        return $this->signatureVerifier;
    }
}
