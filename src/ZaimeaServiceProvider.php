<?php

namespace Zaimea\Socialite;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Zaimea\Socialite\Providers\ZaimeaProvider;

class ZaimeaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Socialite::extend('zaimea', function ($app) {
            $config = $app['config']['services.zaimea'];

            return Socialite::buildProvider(
                ZaimeaProvider::class,
                $config
            );
        });
    }
}
