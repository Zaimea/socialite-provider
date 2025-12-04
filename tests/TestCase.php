<?php

declare(strict_types=1);

namespace Zaimea\Socialite\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Zaimea\Socialite\ZaimeaServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ZaimeaServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('services.zaimea', [
            'client_id'     => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'redirect'      => 'https://zaimea.com/auth/callback',
            'version'       => 'v1',
            'scopes'        => 'user group',
        ]);
    }
}
