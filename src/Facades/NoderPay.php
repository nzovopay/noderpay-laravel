<?php

declare(strict_types=1);

namespace NoderPay\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use NoderPay\NoderPay as NoderPayClient;
use NoderPay\Resources\Invoices;
use NoderPay\Resources\Payments;
use NoderPay\Resources\Stores;
use NoderPay\Resources\Wallets;
use NoderPay\Resources\Webhooks;

/**
 * @method static Invoices invoices()
 * @method static Stores stores()
 * @method static Payments payments()
 * @method static Wallets wallets()
 * @method static Webhooks webhooks()
 * @method static \NoderPay\Config config()
 *
 * @see \NoderPay\NoderPay
 */
final class NoderPay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NoderPayClient::class;
    }
}
