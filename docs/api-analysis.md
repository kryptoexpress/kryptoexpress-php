# API Analysis

## Sources

- OpenAPI: `https://kryptoexpress.pro/api/swagger/documentation.yaml`
- Practical docs: `https://raw.githubusercontent.com/kryptoexpress/kryptoexpress/refs/heads/main/api-docs.md`

## Base URL

- `https://kryptoexpress.pro/api`

## Authentication

- Protected endpoints use `X-Api-Key`.
- Public endpoints do not require `X-Api-Key`.
- Protected endpoints also expect `Content-Type: application/json` for JSON `POST` requests.

## Endpoints

### Payments

- `POST /payment`
- `GET /payment?hash=...`

### Wallet

- `GET /wallet`
- `POST /wallet/withdrawal`

### Currencies

- `GET /currency`
- `GET /cryptocurrency`
- `GET /cryptocurrency/all`
- `GET /cryptocurrency/stable`
- `GET /cryptocurrency/price?cryptoCurrency=BTC,ETH&fiatCurrency=USD`

## Core Models

### Payment request

- `paymentType`
- `cryptoCurrency`
- `fiatCurrency`
- `fiatAmount` for `PAYMENT`
- `callbackUrl`
- `callbackSecret`

### Payment response

- `id`
- `paymentType`
- `fiatCurrency`
- `fiatAmount`
- `cryptoAmount`
- `cryptoCurrency`
- `expireDatetime`
- `createDatetime`
- `paidAt`
- `address`
- `isPaid`
- `isWithdrawn`
- `hash`
- `callbackUrl`

### Withdrawal request

- `withdrawType`
- `paymentId` for `SINGLE`
- `cryptoCurrency`
- `toAddress`
- `onlyCalculate`

### Withdrawal response

- `id`
- `withdrawType`
- `paymentId`
- `cryptoCurrency`
- `toAddress`
- `txIdList`
- `receivingAmount`
- `blockchainFeeAmount`
- `serviceFeeAmount`
- `onlyCalculate`
- `totalWithdrawalAmount`
- `createDatetime`

## Business Rules Chosen For SDK

- `PAYMENT` requires `fiatAmount`.
- `DEPOSIT` must not include `fiatAmount`.
- Stablecoins support only `PAYMENT`.
- Stablecoin payments are exact-payment flows by business semantics.
- Minimum amount is validated client-side only for `USD` payments.
- Non-USD payments are sent without local minimum threshold validation.
- Callback signatures use `X-Signature` and `HMAC-SHA512` over compact JSON.
- Payment lookup is public and keyed by `hash`.
- Withdrawal dry-run is represented by `onlyCalculate = true`.

## Inconsistencies And Ambiguities

- Practical docs list native currencies as `BTC`, `LTC`, `ETH`, `SOL`, `BNB`, `DOGE`, but OpenAPI also includes `TRX`.
- Practical docs list stablecoins on `ERC20`, `BEP20`, and `SOL`, but OpenAPI also includes `USDT_TRC20`.
- OpenAPI enum examples for `/cryptocurrency` and `/cryptocurrency/stable` are broader than the practical guide and appear copy-pasted.
- OpenAPI does not encode the practical stablecoin business rules or the exact-payment semantics strongly enough.
- OpenAPI describes errors only partially. Practical docs give operational rules, but not a full error matrix.
- The API has no dedicated fiat-to-fiat conversion endpoint, which makes non-USD client-side threshold validation undesirable.

## Safe SDK Decisions

- Prefer practical docs for business semantics.
- Keep runtime currency discovery server-driven instead of hardcoding the full enum set.
- Detect stablecoins by business naming convention (`USDT_*`, `USDC_*`) for validation.
- Apply local minimum validation only to `USD` payments and let non-USD payments be validated by the API.
