<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\DTO\Wallet;

use KryptoExpress\SDK\Enum\WithdrawType;
use KryptoExpress\SDK\Support\ArrayAccessor;

final class Withdrawal
{
    /**
     * @param list<string> $txIdList
     */
    public function __construct(
        public readonly ?int $id,
        public readonly WithdrawType $withdrawType,
        public readonly ?int $paymentId,
        public readonly string $cryptoCurrency,
        public readonly string $toAddress,
        public readonly array $txIdList,
        public readonly ?float $receivingAmount,
        public readonly ?float $blockchainFeeAmount,
        public readonly ?float $serviceFeeAmount,
        public readonly bool $onlyCalculate,
        public readonly ?float $totalWithdrawalAmount,
        public readonly int $createDatetime,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            ArrayAccessor::nullableInt($payload, 'id'),
            WithdrawType::from(ArrayAccessor::string($payload, 'withdrawType')),
            ArrayAccessor::nullableInt($payload, 'paymentId'),
            ArrayAccessor::string($payload, 'cryptoCurrency'),
            ArrayAccessor::string($payload, 'toAddress'),
            ArrayAccessor::stringList($payload, 'txIdList'),
            ArrayAccessor::nullableFloat($payload, 'receivingAmount'),
            ArrayAccessor::nullableFloat($payload, 'blockchainFeeAmount'),
            ArrayAccessor::nullableFloat($payload, 'serviceFeeAmount'),
            ArrayAccessor::bool($payload, 'onlyCalculate'),
            ArrayAccessor::nullableFloat($payload, 'totalWithdrawalAmount'),
            ArrayAccessor::int($payload, 'createDatetime'),
        );
    }
}
