<?php

namespace App\Providers;

class CustomInstagramProvider extends \SocialiteProviders\Instagram\Provider
{
    /**
     * The auth URL
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://www.instagram.com/oauth/authorize', $state);
    }

    /**
     * The user fields being requested.
     * We purposefully omit profile_picture_url because it frequently causes
     * "Unsupported request - method type: get" errors with the Instagram Basic Display API.
     *
     * @var array
     */
    protected $fields = [
        'id',
        'username',
        'profile_picture_url',
    ];

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new \SocialiteProviders\Manager\OAuth2\User)->setRaw($user)->map([
            'id'            => $user['id'] ?? null,
            'name'          => $user['username'] ?? null,
            'account_type'  => $user['account_type'] ?? null,
            'media_count'   => $user['media_count'] ?? null,
            'avatar'        => $user['profile_picture_url'] ?? null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $queryParameters = [
            'access_token' => $token,
            'fields'       => implode(',', $this->fields),
        ];

        // Intentionally omit appsecret_proof as it often causes "API access blocked" 
        // if the client secret contains whitespace or is mismatched.

        $response = $this->getHttpClient()->get('https://graph.instagram.com/v21.0/me', [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
            \GuzzleHttp\RequestOptions::QUERY => $queryParameters,
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}
