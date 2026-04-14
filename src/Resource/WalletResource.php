<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Resource;

use KryptoExpress\SDK\DTO\Wallet\WalletBalance;
use KryptoExpress\SDK\DTO\Wallet\Withdrawal;
use KryptoExpress\SDK\DTO\Wallet\WithdrawalRequest;

final class WalletResource extends AbstractResource
{
    public function get(): WalletBalance
    {
        return WalletBalance::fromArray($this->transport->request('GET', '/wallet'));
    }

    public function withdraw(WithdrawalRequest $request): Withdrawal
    {
        return Withdrawal::fromArray($this->transport->request('POST', '/wallet/withdrawal', json: $request->toArray()));
    }

    public function calculate(WithdrawalRequest $request): Withdrawal
    {
        return $this->withdraw($request->withOnlyCalculate(true));
    }

    public function withdrawAll(string $cryptoCurrency, string $toAddress): Withdrawal
    {
        return $this->withdraw(WithdrawalRequest::all($cryptoCurrency, $toAddress));
    }

    public function withdrawSingle(int $paymentId, string $cryptoCurrency, string $toAddress): Withdrawal
    {
        return $this->withdraw(WithdrawalRequest::single($paymentId, $cryptoCurrency, $toAddress));
    }

    public function calculateAll(string $cryptoCurrency, string $toAddress): Withdrawal
    {
        return $this->calculate(WithdrawalRequest::all($cryptoCurrency, $toAddress));
    }

    public function calculateSingle(int $paymentId, string $cryptoCurrency, string $toAddress): Withdrawal
    {
        return $this->calculate(WithdrawalRequest::single($paymentId, $cryptoCurrency, $toAddress));
    }
}
