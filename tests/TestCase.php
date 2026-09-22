<?php

declare(strict_types=1);

namespace NoderPay\Laravel\Tests;

use NoderPay\Laravel\Facades\NoderPay as NoderPayFacade;
use NoderPay\Laravel\NoderPayServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [NoderPayServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return ['NoderPay' => NoderPayFacade::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('noderpay.api_key', 'np_test_key');
        $app['config']->set('noderpay.store_id', 'test_store');
        $app['config']->set('noderpay.base_url', 'https://api.noderpay.com');
    }
}
