<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Resource;

use KryptoExpress\SDK\Contracts\TransportInterface;
use KryptoExpress\SDK\DTO\Payment\CreatePaymentRequest;
use KryptoExpress\SDK\DTO\Payment\Payment;
use KryptoExpress\SDK\Rules\PaymentRulesValidator;

final class PaymentsResource extends AbstractResource
{
    public function __construct(TransportInterface $transport, private readonly PaymentRulesValidator $rulesValidator)
    {
        parent::__construct($transport);
    }

    public function create(CreatePaymentRequest $request): Payment
    {
        $this->rulesValidator->validate($request);

        return Payment::fromArray($this->transport->request('POST', '/payment', json: $request->toArray()));
    }

    public function createPayment(
        string $cryptoCurrency,
        string $fiatCurrency,
        float $fiatAmount,
        string $callbackUrl,
        ?string $callbackSecret = null,
    ): Payment {
        return $this->create(CreatePaymentRequest::payment(
            $cryptoCurrency,
            $fiatCurrency,
            $fiatAmount,
            $callbackUrl,
            $callbackSecret,
        ));
    }

    public function createDeposit(
        string $cryptoCurrency,
        string $fiatCurrency,
        string $callbackUrl,
        ?string $callbackSecret = null,
    ): Payment {
        return $this->create(CreatePaymentRequest::deposit(
            $cryptoCurrency,
            $fiatCurrency,
            $callbackUrl,
            $callbackSecret,
        ));
    }

    public function getByHash(string $hash): Payment
    {
        return Payment::fromArray($this->transport->request('GET', '/payment', ['hash' => $hash], authenticated: false));
    }
}
