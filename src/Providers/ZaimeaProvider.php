<?php

namespace Zaimea\Socialite\Providers;

use GuzzleHttp\RequestOptions;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class ZaimeaProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['user'];

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
}
