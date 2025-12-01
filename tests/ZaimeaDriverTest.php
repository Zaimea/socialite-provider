<?php

declare(strict_types=1);

namespace Zaimea\Socialite\Tests;

use Orchestra\Testbench\TestCase;
use Zaimea\Socialite\ZaimeaServiceProvider;

class ZaimeaDriverTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ZaimeaServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('services.zaimea', [
            'client_id' => 'testclient',
            'client_secret' => 'testsecret',
            'redirect' => 'https://app.test/callback',
            'pkce' => true,
            'scopes' => 'openid profile email',
        ]);
    }

    public function test_provider_registration()
    {
        $this->assertTrue(class_exists('\Zaimea\\Socialite\\ZaimeaProvider'));

        $manager = $this->app->make(\Laravel\Socialite\SocialiteManager::class);
        $driver = $manager->driver('zaimea');

        $this->assertNotNull($driver);
    }
}
