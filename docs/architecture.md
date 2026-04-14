# Architecture Plan

## Package Structure

- `src/KryptoExpressClient.php`
- `src/ClientConfig.php`
- `src/Contracts/*`
- `src/Http/*`
- `src/Resource/*`
- `src/DTO/*`
- `src/Enum/*`
- `src/Error/*`
- `src/Rules/*`
- `src/Support/*`
- `src/Webhook/*`
- `tests/*`

## Public Surface

- `KryptoExpressClient`
- `payments()`
- `wallet()`
- `currencies()`
- `fiat()`
- `webhook()`

## Internal Separation

- `client`: high-level entry point and configuration.
- `resources`: business-oriented API groups.
- `transport`: HTTP abstraction and implementations.
- `dto`: typed request and response payloads.
- `rules`: business-rule validation and minimum amount policy.
- `errors`: transport and domain exceptions.
- `webhook`: callback signature verification.

## WooCommerce Boundary

The future WooCommerce plugin should own:

- WordPress hooks
- WooCommerce gateway classes
- settings UI
- callback route wiring
- order-status mapping
- merchant-side idempotency storage

The core SDK should own:

- request building
- transport
- payment and wallet resources
- DTO hydration
- API error mapping
- payment mode validation
- minimum amount validation
- callback signature verification
