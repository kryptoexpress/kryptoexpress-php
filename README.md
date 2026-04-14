# KryptoExpress PHP SDK

PHP SDK for the KryptoExpress API.

This package is framework-agnostic. It works in plain PHP and can be used as the core layer for framework or CMS integrations, including a future WooCommerce plugin.

## Install

```bash
composer require kryptoexpress/kryptoexpress-php
```

For PSR-18 mode install a PSR-18 client and PSR-17 factory, for example:

```bash
composer require symfony/http-client nyholm/psr7
```

## Requirements

- PHP 8.2 or newer
- `ext-json`
- `ext-hash`

## Quick Start

```php
<?php

use KryptoExpress\SDK\KryptoExpressClient;

$client = new KryptoExpressClient('your-api-key');

$payment = $client->payments()->createPayment(
    cryptoCurrency: 'BTC',
    fiatCurrency: 'USD',
    fiatAmount: 10.0,
    callbackUrl: 'https://merchant.example/callback',
    callbackSecret: 'shared-secret',
);

$wallet = $client->wallet()->get();
$prices = $client->currencies()->getPrices(['BTC', 'ETH'], 'USD');
$fiatCurrencies = $client->fiat()->list();
```

## Client Methods

```php
$client->payments()->create($request);
$client->payments()->createPayment(...);
$client->payments()->createDeposit(...);
$client->payments()->getByHash(...);

$client->wallet()->get();
$client->wallet()->withdraw(...);
$client->wallet()->calculate(...);
$client->wallet()->withdrawAll(...);
$client->wallet()->withdrawSingle(...);
$client->wallet()->calculateAll(...);
$client->wallet()->calculateSingle(...);

$client->currencies()->listAll();
$client->currencies()->listNative();
$client->currencies()->listStable();
$client->currencies()->getPrices(...);

$client->fiat()->list();
```

## Payment Rules

- `PAYMENT` requires `fiatAmount`.
- `DEPOSIT` must not send `fiatAmount`.
- Stablecoins support only `PAYMENT`.
- Stablecoin payments are exact-payment only.
- Client-side minimum amount validation is applied only to `USD` payments.
- For `USD`, the minimum amount is `1.00`.
- Non-USD payments are sent to the API without local minimum threshold validation.

## Fiat Conversion

The SDK no longer requires a fiat converter for normal non-USD payment creation.
If you keep using a converter abstraction in your application, treat it as an optional extension rather than a required part of the default payment flow.

```php
<?php

use KryptoExpress\SDK\ClientConfig;
use KryptoExpress\SDK\KryptoExpressClient;
use KryptoExpress\SDK\Rules\StaticFiatConverter;

$client = new KryptoExpressClient(
    'your-api-key',
    new ClientConfig(
        fiatConverter: new StaticFiatConverter([
            'EUR' => 0.91,
            'GBP' => 0.79,
        ]),
    ),
);
```

You can also disable the remaining USD client-side minimum validation:

```php
<?php

use KryptoExpress\SDK\ClientConfig;
use KryptoExpress\SDK\KryptoExpressClient;

$client = new KryptoExpressClient(
    'your-api-key',
    new ClientConfig(validateMinimumAmounts: false),
);
```

## Webhook Signatures

- Header: `X-Signature`
- Algorithm: `HMAC-SHA512`
- Message: compact raw JSON body
- Key: `callbackSecret`

```php
<?php

$isValid = $client->webhook()->verify($rawBody, 'callback-secret', $_SERVER['HTTP_X_SIGNATURE'] ?? null);
```

## Transport

- `NativeTransport` is the default and works without extra runtime packages.
- `Psr18Transport` can be used when you already have a PSR-18 client stack.

## Using A PSR-18 Client

```php
<?php

use KryptoExpress\SDK\KryptoExpressClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;

$client = KryptoExpressClient::withPsr18(
    'your-api-key',
    new Psr18Client(),
    new Psr17Factory(),
);
```

## Notes

- Practical docs say native coins are `BTC`, `LTC`, `ETH`, `SOL`, `BNB`, `DOGE`, while OpenAPI also includes `TRX`.
- Practical docs list stablecoins on `ERC20`, `BEP20`, and `SOL`, while OpenAPI also includes `USDT_TRC20`.
- OpenAPI examples for `/cryptocurrency` and `/cryptocurrency/stable` contain broader enums than the practical guide suggests.
- The practical docs are authoritative for `PAYMENT` vs `DEPOSIT`, stablecoin semantics, minimum amount policy, and webhook signature behavior.

## WooCommerce

This package does not include WordPress or WooCommerce code. A WooCommerce plugin should use this SDK as its core API layer and keep hooks, settings, gateway classes, callback routing, and order-status mapping in a separate integration package.
