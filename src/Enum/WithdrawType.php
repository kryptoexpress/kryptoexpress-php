<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Enum;

enum WithdrawType: string
{
    case ALL = 'ALL';
    case SINGLE = 'SINGLE';
}
