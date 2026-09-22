<?php

declare(strict_types=1);

namespace NoderPay\Laravel;

use Illuminate\Support\ServiceProvider;
use NoderPay\Laravel\Console\TestConnectionCommand;
use NoderPay\Laravel\Support\NoderPayManager;
use NoderPay\NoderPay;
use Psr\Log\LoggerInterface;

final class NoderPayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/noderpay.php', 'noderpay');

        $this->app->singleton(NoderPay::class, function ($app) {
            $logger = $app->bound(LoggerInterface::class) ? $app->make(LoggerInterface::class) : null;

            $manager = new NoderPayManager($app['config']['noderpay'] ?? [], $logger);

            return $manager->make();
        });

        $this->app->alias(NoderPay::class, 'noderpay');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/noderpay.php' => config_path('noderpay.php'),
            ], 'noderpay-config');

            $this->commands([
                TestConnectionCommand::class,
            ]);
        }
    }

    public function provides(): array
    {
        return [NoderPay::class, 'noderpay'];
    }
}
