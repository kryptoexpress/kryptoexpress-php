# KryptoExpress PHP SDK

Framework-agnostic PHP SDK for the KryptoExpress API.

## Goals

- Composer-friendly package for Packagist.
- Works in plain PHP without Laravel, Symfony, Yii, WordPress, or WooCommerce.
- Keeps business terminology aligned with other KryptoExpress SDKs.
- Leaves WooCommerce for a separate integration layer.

## Install

```bash
composer require kryptoexpress/kryptoexpress-php-sdk
```

For PSR-18 mode install a PSR-18 client and PSR-17 factory, for example:

```bash
composer require symfony/http-client nyholm/psr7
```

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

## Public API

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

## Business Rules

- `PAYMENT` requires `fiatAmount`.
- `DEPOSIT` must not send `fiatAmount`.
- Stablecoins support only `PAYMENT`.
- Stablecoin payments are exact-payment flows in KryptoExpress business semantics.
- Minimum payment amount must be at least the equivalent of `1 USD`.
- If minimum validation is enabled and fiat is not `USD`, provide a fiat converter or the SDK will throw `CurrencyConversionError`.

## Fiat Conversion

The API does not expose a dedicated fiat-to-fiat endpoint, so minimum amount validation uses an explicit converter abstraction.

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

## Webhook Signatures

- Header: `X-Signature`
- Algorithm: `HMAC-SHA512`
- Message: compact raw JSON body
- Key: `callbackSecret`

```php
<?php

$isValid = $client->webhook()->verify($rawBody, 'callback-secret', $_SERVER['HTTP_X_SIGNATURE'] ?? null);
```

## Transport Options

- `NativeTransport` is the default and works without extra runtime packages.
- `Psr18Transport` can be used when you already have a PSR-18 client stack.

## Known Source Ambiguities

- Practical docs say native coins are `BTC`, `LTC`, `ETH`, `SOL`, `BNB`, `DOGE`, while OpenAPI also includes `TRX`.
- Practical docs list stablecoins on `ERC20`, `BEP20`, and `SOL`, while OpenAPI also includes `USDT_TRC20`.
- OpenAPI examples for `/cryptocurrency` and `/cryptocurrency/stable` contain broader enums than the practical guide suggests.
- The practical docs are authoritative for `PAYMENT` vs `DEPOSIT`, stablecoin semantics, minimum amount policy, and webhook signature behavior.

## WooCommerce Integration Boundary

This SDK intentionally does not contain WordPress hooks, WooCommerce gateway classes, admin settings, callback routing, or order-status mapping.
A future WooCommerce plugin should depend on this package and translate WooCommerce events and settings into SDK calls.
