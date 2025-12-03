<?php

namespace Zaimea\Socialite\Providers;

use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class ZaimeaProvider extends AbstractProvider
{
    /**
     * The base URL for Zaimea Accounts API.
     */
    protected string $baseUrl = 'https://accounts.zaimea.com';

    /**
     * The API version.
     */
    protected string $version = 'v1';

    /**
     * The scopes being requested.
     */
    protected $scopes = ['user', 'group'];

    /**
     * Constructor.
     */
    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirect, array $config = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirect);

        if (!empty($config['base_url'])) {
            $this->baseUrl = rtrim($config['base_url'], '/');
        }

        $this->version = $config['version'] ?? $this->version;

        if (!empty($config['scopes'])) {
            $this->scopes = explode(' ', $config['scopes']);
        }
    }

    /**
     * Get the authentication URL for the provider.
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/oauth/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     */
    protected function getTokenUrl(): string
    {
        return $this->baseUrl . '/oauth/token';
    }

    /**
     * Get the raw user for the given access token.
     */
    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get($this->baseUrl . "/api/{$this->version}/user", [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     */
    protected function mapUserToObject(array $user): User
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'] ?? null,
            'nickname' => $user['nickname'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar'] ?? null,
        ]);
    }

    /**
     * Get the POST fields for the token request.
     */
    protected function getTokenFields($code): array
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
