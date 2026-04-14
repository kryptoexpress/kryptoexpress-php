<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Enum;

enum PaymentType: string
{
    case PAYMENT = 'PAYMENT';
    case DEPOSIT = 'DEPOSIT';
}
