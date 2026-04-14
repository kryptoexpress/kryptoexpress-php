<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Rules;

use KryptoExpress\SDK\DTO\Payment\CreatePaymentRequest;
use KryptoExpress\SDK\Enum\PaymentType;
use KryptoExpress\SDK\Error\SDKError;
use KryptoExpress\SDK\Error\UnsupportedPaymentModeError;

final class PaymentRulesValidator
{
    public function __construct(private readonly ?MinimumAmountPolicy $minimumAmountPolicy = null)
    {
    }

    public function validate(CreatePaymentRequest $request): void
    {
        if ($request->paymentType === PaymentType::PAYMENT && $request->fiatAmount === null) {
            throw new SDKError('PAYMENT requests require fiatAmount.');
        }

        if ($request->paymentType === PaymentType::DEPOSIT && $request->fiatAmount !== null) {
            throw new SDKError('DEPOSIT requests must not include fiatAmount.');
        }

        if ($this->isStablecoin($request->cryptoCurrency) && $request->paymentType !== PaymentType::PAYMENT) {
            throw new UnsupportedPaymentModeError('Stablecoins support only PAYMENT mode.');
        }

        if (
            $request->paymentType === PaymentType::PAYMENT
            && $request->fiatAmount !== null
            && $this->minimumAmountPolicy !== null
        ) {
            $this->minimumAmountPolicy->assertSatisfied($request->fiatAmount, $request->fiatCurrency);
        }
    }

    private function isStablecoin(string $cryptoCurrency): bool
    {
        $code = strtoupper($cryptoCurrency);

        return str_starts_with($code, 'USDT_') || str_starts_with($code, 'USDC_');
    }
}
