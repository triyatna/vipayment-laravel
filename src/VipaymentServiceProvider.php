<?php

namespace Triyatna\Vipayment;

use Illuminate\Support\ServiceProvider;
use Triyatna\Vipayment\Console\Commands\InstallVipayment;

class VipaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/vipayment.php',
            'vipayment'
        );

        $this->app->singleton('vipayment', function ($app) {
            $config = $app['config']['vipayment'];
            return new VipaymentClient($config['api_id'], $config['api_key'], $config['base_url']);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/vipayment.php' => config_path('vipayment.php'),
            ], 'vipayment-config');

            // Register the command
            $this->commands([
                InstallVipayment::class,
            ]);
        }
    }
}
