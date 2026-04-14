<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Resource;

use KryptoExpress\SDK\DTO\Currency\CryptoPrice;

final class CurrenciesResource extends AbstractResource
{
    /**
     * @return list<string>
     */
    public function listNative(): array
    {
        return $this->normalizeStringList($this->transport->request('GET', '/cryptocurrency', authenticated: false));
    }

    /**
     * @return list<string>
     */
    public function listAll(): array
    {
        return $this->normalizeStringList($this->transport->request('GET', '/cryptocurrency/all', authenticated: false));
    }

    /**
     * @return list<string>
     */
    public function listStable(): array
    {
        return $this->normalizeStringList($this->transport->request('GET', '/cryptocurrency/stable', authenticated: false));
    }

    /**
     * @param list<string> $cryptoCurrencies
     * @return list<CryptoPrice>
     */
    public function getPrices(array $cryptoCurrencies, string $fiatCurrency): array
    {
        $payload = $this->transport->request(
            'GET',
            '/cryptocurrency/price',
            ['cryptoCurrency' => implode(',', $cryptoCurrencies), 'fiatCurrency' => $fiatCurrency],
            authenticated: false,
        );

        $prices = [];

        foreach ($payload as $item) {
            if (is_array($item)) {
                $prices[] = CryptoPrice::fromArray($item);
            }
        }

        return $prices;
    }

    /**
     * @param array<mixed> $payload
     * @return list<string>
     */
    private function normalizeStringList(array $payload): array
    {
        $result = [];

        foreach ($payload as $item) {
            if (is_string($item)) {
                $result[] = $item;
            }
        }

        return $result;
    }
}
