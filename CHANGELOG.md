# Changelog

All notable changes to this project are documented in this file.

## [Unreleased]

### Added
- `NoderPayServiceProvider` with package auto-discovery, binding a singleton `NoderPay\NoderPay` client built from `config/noderpay.php`.
- `NoderPay` facade resolving the same container-bound singleton.
- `NoderPayManager` support class that constructs the SDK client from Laravel config, with a clear exception if credentials are missing.
- Publishable `config/noderpay.php` reading from `NODERPAY_API_KEY`, `NODERPAY_STORE_ID`, `NODERPAY_BASE_URL`, `NODERPAY_WEBHOOK_SECRET`, and related env vars.
- `php artisan noderpay:test` command, verifying connectivity via the confirmed `Stores::get()` endpoint. Never prints the full API key.
- Tests covering provider registration, config merging/publishing, facade resolution, DI, and the Artisan command (using a mocked HTTP handler, no live credentials required).

### Notes
- This package intentionally contains no NoderPay API logic of its own — all HTTP/DTO/exception logic lives in `noderpay/noderpay-php`, which this package requires and wraps.
- No bundled webhook route/controller is provided; see the README for the recommended pattern, matching the plain-PHP SDK's `examples/webhook.php`.
