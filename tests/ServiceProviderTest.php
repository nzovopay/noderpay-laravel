<?php

declare(strict_types=1);

namespace NoderPay\Laravel\Tests;

use NoderPay\NoderPay;

final class ServiceProviderTest extends TestCase
{
    public function testConfigIsMergedWithDefaults(): void
    {
        $this->assertSame('https://api.noderpay.com', config('noderpay.base_url'));
        $this->assertSame(25, config('noderpay.timeout'));
        $this->assertSame(2, config('noderpay.max_retries'));
    }

    public function testNoderPayIsBoundAsASingleton(): void
    {
        $first = $this->app->make(NoderPay::class);
        $second = $this->app->make(NoderPay::class);

        $this->assertSame($first, $second);
    }

    public function testNoderPayResolvesFromContainerAliasToo(): void
    {
        $viaClass = $this->app->make(NoderPay::class);
        $viaAlias = $this->app->make('noderpay');

        $this->assertSame($viaClass, $viaAlias);
    }

    public function testConfigCanBePublished(): void
    {
        $this->artisan('vendor:publish', [
            '--provider' => \NoderPay\Laravel\NoderPayServiceProvider::class,
            '--tag' => 'noderpay-config',
        ])->assertExitCode(0);

        $this->assertFileExists(config_path('noderpay.php'));
    }

    public function testMissingCredentialsThrowsAClearException(): void
    {
        config(['noderpay.api_key' => null]);
        $this->app->forgetInstance(NoderPay::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/NODERPAY_API_KEY/');

        $this->app->make(NoderPay::class);
    }
}
