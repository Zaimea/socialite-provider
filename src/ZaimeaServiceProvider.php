<?php

namespace Zaimea\Socialite;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Zaimea\Socialite\Providers\ZaimeaProvider;

class ZaimeaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->resolving(SocialiteFactory::class, function ($socialite) {
            $socialite->extend('zaimea', function ($app) {
                $config = $app['config']['services.zaimea'];

                return new ZaimeaProvider(
                    $app['request'],
                    $config['client_id'],
                    $config['client_secret'],
                    $config['redirect'],
                    $config
                );
            });
        });
    }
}
