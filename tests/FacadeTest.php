<?php

declare(strict_types=1);

namespace NoderPay\Laravel\Tests;

use NoderPay\Laravel\Facades\NoderPay as NoderPayFacade;
use NoderPay\NoderPay;

final class FacadeTest extends TestCase
{
    public function testFacadeResolvesSameInstanceAsContainer(): void
    {
        $viaContainer = $this->app->make(NoderPay::class);
        $viaFacade = NoderPayFacade::getFacadeRoot();

        $this->assertSame($viaContainer, $viaFacade);
    }

    public function testFacadeExposesConfig(): void
    {
        $config = NoderPayFacade::config();

        $this->assertSame('test_store', $config->storeId);
        $this->assertSame('https://api.noderpay.com', $config->baseUrl);
    }
}
