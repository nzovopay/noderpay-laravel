<?php

declare(strict_types=1);

namespace NoderPay\Laravel\Console;

use Illuminate\Console\Command;
use NoderPay\Exceptions\NoderPayException;
use NoderPay\NoderPay;

final class TestConnectionCommand extends Command
{
    protected $signature = 'noderpay:test';

    protected $description = 'Test the connection to the NoderPay API using the configured credentials';

    public function handle(NoderPay $noderpay): int
    {
        $config = $noderpay->config();

        $this->info('NoderPay connection test');
        $this->line('API: ' . $config->baseUrl);

        try {
            $store = $noderpay->stores()->get($config->storeId);
        } catch (NoderPayException $e) {
            $this->line('Authentication: FAILED');
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->line('Authentication: OK');
        $this->line('Store: ' . $store->name . ' (' . $store->id . ')');
        $this->line('Status: ' . ($store->isActive() ? 'Connected' : 'Connected, but store is not active (' . $store->status . ')'));

        return self::SUCCESS;
    }
}
