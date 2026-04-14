<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Resource;

final class FiatResource extends AbstractResource
{
    /**
     * @return list<string>
     */
    public function list(): array
    {
        $payload = $this->transport->request('GET', '/currency', authenticated: false);
        $result = [];

        foreach ($payload as $item) {
            if (is_string($item)) {
                $result[] = $item;
            }
        }

        return $result;
    }
}
