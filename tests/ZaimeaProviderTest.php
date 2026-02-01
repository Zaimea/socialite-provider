<?php

declare(strict_types=1);

namespace Zaimea\Socialite\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;

class ZaimeaProviderTest extends TestCase
{
    protected function provider()
    {
        return Socialite::driver('zaimea');
    }

    /** @test */
    public function it_redirects_to_correct_authorization_url()
    {
        $request = $this->provider()->redirect();

        $url = $request->getTargetUrl();

        $this->assertStringStartsWith('https://accounts.zaimea.com/oauth/authorize', $url);
        $this->assertStringContainsString('client_id=test-client-id', $url);
        $this->assertStringContainsString('redirect_uri=https%3A%2F%2Fzaimea.com%2Fauth%2Fcallback', $url);
        $this->assertStringContainsString('scope=user', $url);
        $this->assertStringContainsString('response_type=code', $url);
    }

    /** @test */
    public function it_fetches_access_token_correctly()
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'access_token' => 'mock_access_token',
                'token_type'   => 'Bearer',
                'expires_in'   => 3600,
                'refresh_token' => 'mock_refresh_token',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $client = new Client(['handler' => $handlerStack]);
        $provider = $this->provider();
        $provider->setHttpClient($client);

        $token = $provider->getAccessTokenResponse('auth_code_mock');

        $this->assertArrayHasKey('access_token', $token);
        $this->assertEquals('mock_access_token', $token['access_token']);

        // Verificăm că request-ul a fost POST cu form params corecte
        $request = $container[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('accounts.zaimea.com', $request->getUri()->getHost());
        $this->assertEquals('/oauth/token', $request->getUri()->getPath());

        $body = $request->getBody()->getContents();
        parse_str($body, $params);

        $this->assertEquals('authorization_code', $params['grant_type']);
        $this->assertEquals('test-client-id', $params['client_id']);
        $this->assertEquals('test-client-secret', $params['client_secret']);
        $this->assertEquals('https://zaimea.com/auth/callback', $params['redirect_uri']);
        $this->assertEquals('auth_code_mock', $params['code']);
    }

    /** @test */
    public function it_fetches_user_correctly()
    {
        $mockUserResponse = file_get_contents(__DIR__ . '/fixtures/user-response.json');

        $mock = new MockHandler([
            new Response(200, [], $mockUserResponse),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);
        $provider = $this->provider();
        $provider->setHttpClient($client);

        $user = $provider->userFromToken('mock_token');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(123, $user->getId());
        $this->assertEquals('Laurențiu', $user->getName());
        $this->assertEquals('laurentiu@zaimea.com', $user->getEmail());
        $this->assertEquals('laur', $user->getNickname());
    }

    /** @test */
    public function it_maps_user_fields_correctly()
    {
        $rawUser = [
            'id' => 999,
            'name' => 'Test User',
            'email' => 'test@zaimea.com',
            'nickname' => 'tester',
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($rawUser)),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);
        $provider = $this->provider();
        $provider->setHttpClient($client);

        $user = $provider->userFromToken('token');

        $this->assertEquals(999, $user->getId());
        $this->assertEquals('Test User', $user->getName());
        $this->assertEquals('tester', $user->getNickname());
    }
}
