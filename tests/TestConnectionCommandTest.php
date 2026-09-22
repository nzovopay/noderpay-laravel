<?php

declare(strict_types=1);

namespace NoderPay\Laravel\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use NoderPay\NoderPay;

final class TestConnectionCommandTest extends TestCase
{
    private function bindMockedNoderPay(int $status, array $body): void
    {
        $mockHandler = new MockHandler([
            new Response($status, ['Content-Type' => 'application/json'], json_encode($body)),
        ]);
        $stack = HandlerStack::create($mockHandler);
        $guzzle = new GuzzleClient(['handler' => $stack]);

        $this->app->forgetInstance(NoderPay::class);
        $this->app->instance(NoderPay::class, new NoderPay(
            apiKey: 'np_test_key',
            storeId: 'test_store',
            httpClient: $guzzle,
        ));
    }

    public function testCommandSucceedsAndDoesNotLeakTheApiKey(): void
    {
        $this->bindMockedNoderPay(200, [
            'success' => true,
            'data' => [
                'internal_store_id' => 'test_store',
                'name' => 'My Store',
                'default_currency' => 'USD',
                'status' => 'active',
            ],
        ]);

        $this->artisan('noderpay:test')
            ->expectsOutputToContain('Authentication: OK')
            ->expectsOutputToContain('My Store')
            ->assertExitCode(0);
    }

    public function testCommandFailsCleanlyOnAuthenticationError(): void
    {
        $this->bindMockedNoderPay(401, [
            'message' => 'Invalid API key',
        ]);

        $this->artisan('noderpay:test')
            ->expectsOutputToContain('Authentication: FAILED')
            ->assertExitCode(1);
    }

    public function testCommandOutputNeverContainsTheFullApiKey(): void
    {
        $this->bindMockedNoderPay(200, [
            'success' => true,
            'data' => [
                'internal_store_id' => 'test_store',
                'name' => 'My Store',
                'default_currency' => 'USD',
                'status' => 'active',
            ],
        ]);

        \Illuminate\Support\Facades\Artisan::call('noderpay:test');
        $output = \Illuminate\Support\Facades\Artisan::output();

        $this->assertStringNotContainsString('np_test_key', $output);
    }
}
