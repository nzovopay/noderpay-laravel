# nzovopay/noderpay-laravel

Laravel service provider, facade, config, and Artisan tooling for the [NoderPay](https://noderpay.com) API. This package is a thin wrapper around [`noderpay/noderpay-php`](https://packagist.org/packages/noderpay/noderpay-php) — it contains no NoderPay API logic of its own.

## Requirements

- PHP 8.1+
- Laravel 10, 11, or 12

## Installation

```bash
composer require nzovopay/noderpay-laravel
```

The service provider and `NoderPay` facade are registered automatically via Laravel package discovery — no manual registration needed.

Publish the config file:

```bash
php artisan vendor:publish --provider="NoderPay\Laravel\NoderPayServiceProvider" --tag="noderpay-config"
```

This creates `config/noderpay.php`. Add the following to your `.env`:

```
NODERPAY_API_KEY=np_live_xxxxxxxxx
NODERPAY_STORE_ID=STORE_ID
NODERPAY_BASE_URL=https://api.noderpay.com
NODERPAY_WEBHOOK_SECRET=
```

`NODERPAY_BASE_URL` and `NODERPAY_WEBHOOK_SECRET` are optional. The base URL defaults to the production API; only override it for a sandbox/test environment.

## Usage

### Facade

```php
use NoderPay\Laravel\Facades\NoderPay;

$invoice = NoderPay::invoices()->create([
    'amount' => 35.00,
    'currency' => 'USD',
    'order_id' => (string) $order->id,
    'redirect_url' => route('payment.success'),
]);

return redirect()->away($invoice->checkoutUrl);
```

### Dependency injection

```php
use NoderPay\NoderPay;

final class BillingService
{
    public function __construct(private NoderPay $noderpay) {}

    public function createInvoice(array $data)
    {
        return $this->noderpay->invoices()->create($data);
    }
}
```

Both the facade and constructor injection resolve the **same singleton instance** from the container — there's only ever one configured client per request.

### Testing the connection

```bash
php artisan noderpay:test
```

```
NoderPay connection test
API: https://api.noderpay.com
Authentication: OK
Store: My Store (STORE_ID)
Status: Connected
```

This command never prints your full API key. On failure, it reports the API's error message and exits non-zero, so it's safe to use in deploy-verification scripts.

## Webhooks

This package does not register a webhook route or controller for you — NoderPay account setups and route naming vary too much per app to guess safely. Register your own route and controller using the facade:

```php
// routes/web.php or routes/api.php
Route::post('/webhooks/noderpay', NoderPayWebhookController::class);
```

```php
use Illuminate\Http\Request;
use NoderPay\Laravel\Facades\NoderPay;

final class NoderPayWebhookController
{
    public function __invoke(Request $request)
    {
        $raw = $request->getContent();
        $signature = $request->header('Merchant-Sig');

        if (!NoderPay::webhooks()->verify($raw, $signature, config('noderpay.webhook_secret'))) {
            abort(401);
        }

        $event = NoderPay::webhooks()->parse($raw);

        if ($event->type === 'InvoiceSettled' && $event->invoice !== null) {
            // Look up the local order by $event->invoice->orderId, confirm it
            // matches your records, then dispatch a job to mark it paid.
            // Do this via a queued job, not inline here, so the webhook
            // response returns quickly.
        }

        return response()->json(['ok' => true]);
    }
}
```

**Remember to exclude this route from CSRF protection** (Laravel's `VerifyCsrfToken` middleware), since NoderPay's webhook requests won't carry a Laravel session/CSRF token.

**Signature verification caveat**: the `Merchant-Sig` header format (`sha256=<hex digest>`) and algorithm (HMAC-SHA256) are confirmed, but the exact signing input has only been verified as "the raw request body" — see the SDK's own README for details. Always pass `$request->getContent()` (the raw body), never `$request->all()` or re-encoded JSON, since re-serialization can change byte-for-byte content and break signature verification even with a correct secret.

## Configuration reference

| Config key | Env variable | Default |
|---|---|---|
| `api_key` | `NODERPAY_API_KEY` | — (required) |
| `store_id` | `NODERPAY_STORE_ID` | — (required) |
| `base_url` | `NODERPAY_BASE_URL` | `https://api.noderpay.com` |
| `webhook_secret` | `NODERPAY_WEBHOOK_SECRET` | `null` |
| `timeout` | `NODERPAY_TIMEOUT` | `25` |
| `connect_timeout` | `NODERPAY_CONNECT_TIMEOUT` | `5` |
| `max_retries` | `NODERPAY_MAX_RETRIES` | `2` |
| `debug` | `NODERPAY_DEBUG` | `false` |

If `api_key` or `store_id` is missing when the container tries to resolve the `NoderPay` client, a `RuntimeException` is thrown immediately with a message telling you which env vars to set — not a confusing failure deep inside an HTTP call.

## Testing this package

```bash
composer install
composer test
```

Tests use [Orchestra Testbench](https://github.com/orchestral/testbench) to boot a minimal Laravel app, and a mocked HTTP handler for the Artisan command test — no live NoderPay credentials or network access required.

## License

MIT. See `LICENSE`.
