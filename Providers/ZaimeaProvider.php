<?php

namespace Zaimea\Socialite\Providers;

use Illuminate\Http\Request;
use GuzzleHttp\ClientInterface;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class ZaimeaProvider extends AbstractProvider
{
    /**
     * Separator for scopes
     */
    protected $scopeSeparator = ' ';

    /**
     * Scopes
     */
    protected $scopes = ['user'];

    /**
     * Base URL for OAuth
     */
    protected string $baseUrl;

    /**
     * Config driver
     */
    protected array $driverConfig = [];

    /**
     * API Version
     */
    protected string $version;

    /**
     * Constructor
     */
    public function __construct(
        Request $request,
        $clientId,
        $clientSecret,
        $redirectUrl = null,
        ?ClientInterface $guzzle = null,
        array $driverConfig = []
    ) {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl);

        $this->baseUrl = rtrim('https://accounts.zaimea.com', '/');
        $this->driverConfig = $driverConfig ?: config('services.zaimea');
        $this->version = $this->driverConfig['version'] ?? 'v1';

        if ($guzzle) {
            $this->setHttpClient($guzzle);
        }

        if (!empty($this->driverConfig['scopes'])) {
            $this->scopes = explode(' ', $this->driverConfig['scopes']);
        }
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string  $state
     * @return string
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            $this->baseUrl . '/oauth/authorize',
            $state
        );
    }

    /**
     * Get the validation URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl(): string
    {
        return $this->baseUrl . '/oauth/token';
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    public function getUserByToken($token): mixed
    {
        $url = rtrim($this->baseUrl, '/') . "/api/{$this->version}/auth/user";

        $response = $this->getHttpClient()->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        $body = json_decode((string)$response->getBody(), true);

        return $body['user'] ?? $body['data'] ?? $body;
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array  $user
     * @return User
     */
    protected function mapUserToObject(array $user): User
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'] ?? $user['uuid'] ?? null,
            'nickname' => $user['nickname'] ?? null,
            'name' => $user['name'] ?? $user['full_name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar'] ?? $user['profile_photo_url'] ?? null,
        ]);
    }

    /**
     * Token fields
     */
    protected function getTokenFields($code): array
    {
        $fields = parent::getTokenFields($code);
        $fields['grant_type'] = 'authorization_code';
        return $fields;
    }
}
