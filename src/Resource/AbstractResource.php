<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Resource;

use KryptoExpress\SDK\Contracts\TransportInterface;

abstract class AbstractResource
{
    public function __construct(protected readonly TransportInterface $transport)
    {
    }
}
