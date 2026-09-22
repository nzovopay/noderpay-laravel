<?php

declare(strict_types=1);

namespace NoderPay\Laravel\Support;

use NoderPay\NoderPay;
use Psr\Log\LoggerInterface;

final class NoderPayManager
{
    public function __construct(
        private readonly array $config,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function make(): NoderPay
    {
        $apiKey = (string) ($this->config['api_key'] ?? '');
        $storeId = (string) ($this->config['store_id'] ?? '');

        if ($apiKey === '' || $storeId === '') {
            throw new \RuntimeException(
                'NoderPay is not configured. Set NODERPAY_API_KEY and NODERPAY_STORE_ID in your .env file, or publish and edit config/noderpay.php.'
            );
        }

        return new NoderPay(
            apiKey: $apiKey,
            storeId: $storeId,
            baseUrl: $this->config['base_url'] ?? null,
            httpClient: null,
            logger: $this->logger,
            connectTimeout: (float) ($this->config['connect_timeout'] ?? 5),
            timeout: (float) ($this->config['timeout'] ?? 25),
            maxRetries: (int) ($this->config['max_retries'] ?? 2),
            debug: (bool) ($this->config['debug'] ?? false),
        );
    }
}
