# Changelog

All notable changes to this project will be documented in this file.

## [0.1.3] - 2026-04-14

- Limited client-side minimum amount validation to `USD` payments only.
- Allowed non-USD `PAYMENT` requests to pass local validation without a fiat converter.
- Updated tests and documentation for the new minimum amount behavior.

## [0.1.1] - 2026-04-14

- Renamed the package to `kryptoexpress/kryptoexpress-php`.
- Regenerated `composer.lock` for PHP 8.2 compatible CI installs.
- Polished repository metadata and README.

## [0.1.0] - 2026-04-14

- Initial production-oriented SDK scaffold.
- Added framework-agnostic client, transport, resources, DTOs, business-rule validation, webhook signature verification, tests, and CI.
