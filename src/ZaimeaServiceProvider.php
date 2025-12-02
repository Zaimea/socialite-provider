<?php

namespace Zaimea\Socialite;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\SocialiteManager;
use Zaimea\Socialite\Providers\ZaimeaProvider;

class ZaimeaServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->resolving(SocialiteManager::class, function ($manager) {
            $manager->extend('zaimea', function ($app) use ($manager) {
                $config = $app['config']->get('services.zaimea');
                return new ZaimeaProvider(
                    $app['request'],
                    $config['client_id'],
                    $config['client_secret'],
                    $config['redirect'],
                    null,
                    $config
                );
            });
        });
    }
}
