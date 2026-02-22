<?php

namespace Zaimea\Socialite\Providers;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class ZaimeaProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        'user:read',
    ];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://accounts.zaimea.com/oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://accounts.zaimea.com/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $userUrl = 'https://accounts.zaimea.com/api/v1/auth/user';

        $response = $this->getHttpClient()->get(
            $userUrl, $this->getRequestOptions($token)
        );

        $user = json_decode($response->getBody(), true);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['id'] ?? null,
            'nickname' => $user['username'] ?? null,
            'name'     => $user['name'] ?? null,
            'email'    => $user['email'] ?? null,
            'avatar'   => $user['profile_photo_url'] ?? null,
        ]);
    }

    /**
     * Get the default options for an HTTP request.
     *
     * @param  string  $token
     * @return array
     */
    protected function getRequestOptions($token)
    {
        return [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '. $token,
            ],
        ];
    }

    /**
     * Refresh the access token using refresh token
     *
     * @param  string  $refreshToken
     * @return array
     * @throws \Exception
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        $response = Http::asForm()->post($this->getTokenUrl(), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to refresh token: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Verify if token is still valid
     *
     * @param  string  $token
     * @return bool
     */
    public function verifyToken(string $token): bool
    {
        try {
            $response = Http::withToken($token)
                ->timeout(5)
                ->get('https://accounts.zaimea.com/api/v1/auth/user');

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}