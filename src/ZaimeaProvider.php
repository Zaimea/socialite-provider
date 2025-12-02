<?php

namespace Zaimea\Socialite;

use GuzzleHttp\ClientInterface;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class ZaimeaProvider extends AbstractProvider
{
    protected $scopeSeparator = ' ';
    protected $scopes = ['user'];
    protected string $baseUrl = '';
    protected array $driverConfig = [];

    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl = null, ?ClientInterface $guzzle = null, array $driverConfig = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl);

        $this->baseUrl = rtrim('https://accounts.zaimea.com', '/');
        $this->driverConfig = $driverConfig ?: config('services.zaimea');

        if ($guzzle) {
            $this->setHttpClient($guzzle);
        }

        if (! empty($this->driverConfig['scopes'])) {
            $this->scopes = explode(' ', $this->driverConfig['scopes']);
        }
    }

    protected function getAuthUrl($state)
    {
        $url = $this->buildAuthUrlFromBase($this->baseUrl.'/oauth/authorize', $state);

        // if PKCE enabled, append code_challenge fields; the AbstractProvider's getCodeVerifier
        // returns null by default — we override getCodeVerifier() via trait below if needed.
        $params = [];
        if (! empty($this->driverConfig['pkce'])) {
            $verifier = $this->getCodeVerifier();
            if ($verifier) {
                $params['code_challenge'] = $this->getCodeChallenge();
                $params['code_challenge_method'] = 'S256';
            }
        }

        if (! empty($params)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
        }

        return $url;
    }

    protected function getTokenUrl()
    {
        return $this->baseUrl.'/oauth/token';
    }

    public function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->baseUrl.'/api/user', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);

        return $body['user'] ?? $body;
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'] ?? $user['uuid'] ?? null,
            'nickname' => $user['nickname'] ?? null,
            'name' => $user['name'] ?? $user['full_name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar'] ?? null,
        ]);
    }

    protected function getTokenFields($code)
    {
        $fields = parent::getTokenFields($code);
        $fields['grant_type'] = 'authorization_code';

        // if using PKCE, include code_verifier
        if (! empty($this->driverConfig['pkce'])) {
            $verifier = session()->pull('zaimea_code_verifier');
            if ($verifier) {
                $fields['code_verifier'] = $verifier;
            }
        }

        return $fields;
    }

    protected function getCodeVerifier(): string
    {
        $verifier = session()->get('zaimea_code_verifier');
        if (is_string($verifier) && $verifier !== '') {
            return $verifier;
        }

        $verifier = bin2hex(random_bytes(64));
        session(['zaimea_code_verifier' => $verifier]);

        return $verifier;
    }

    /**
     * Return the code challenge for the current verifier.
     *
     * Signature must match the parent AbstractProvider::getCodeChallenge()
     */
    protected function getCodeChallenge(): string
    {
        $verifier = $this->getCodeVerifier();

        // S256 code challenge
        $hash = hash('sha256', $verifier, true);
        $challenge = rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');

        return $challenge;
    }

    /**
     * Return the code challenge method used.
     * AbstractProvider uses getCodeChallengeMethod() when PKCE is enabled.
     */
    protected function getCodeChallengeMethod(): string
    {
        return 'S256';
    }
}
