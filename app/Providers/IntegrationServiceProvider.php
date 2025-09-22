<?php

namespace App\Providers;

use App\Integrations\PrestaShop\PrestaShopClient;
use App\Integrations\InPost\InPostClient;
use App\Integrations\SMSAPI\SMSApiClient;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class IntegrationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PrestaShopClient::class, function ($app) {
            $config = $app['config']['integrations.prestashop'];
            return new PrestaShopClient(
                new Client(['timeout' => $config['timeout']]),
                $config['base_url'],
                $config['api_key']
            );
        });

        $this->app->singleton(InPostClient::class, function ($app) {
            $config = $app['config']['integrations.inpost'];
            return new InPostClient(
                new Client(['timeout' => $config['timeout']]),
                $config['base_url'],
                $config['api_token']
            );
        });

        $this->app->singleton(SMSApiClient::class, function ($app) {
            $config = $app['config']['integrations.smsapi'];
            return new SMSApiClient(
                new Client(['timeout' => $config['timeout']]),
                $config['base_url'],
                $config['token']
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
