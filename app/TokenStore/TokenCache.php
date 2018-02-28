<?php

namespace App\TokenStore;

class TokenCache
{
    public function storeTokens($access_token, $refresh_token, $expires)
    {
        session(['access_token' => $access_token]);
        session(['refresh_token' => $refresh_token]);
        session(['token_expires' => $expires]);
    }

    public function clearTokens()
    {
        session()->forget('access_token');
        session()->forget('refresh_token');
        session()->forget('token_expires');
    }

    public function getAccessToken()
    {
        // Check if tokens exist
        if (!session('access_token') || !session('refresh_token') || !session('token_expires')) {
            dd('null');
            return null;
        }

        // Check if token is expired
        // Get current time + 5 minutes (to allow for time differences)
        $now = time() + 300;
        if (session('token_expires') <= $now) {
            // Token is expired (or very close to it)
            // so let's refresh

            // Initialize the OAuth client
            $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId' => env('OAUTH_APP_ID'),
                'clientSecret' => env('OAUTH_APP_PASSWORD'),
                'redirectUri' => env('OAUTH_REDIRECT_URI'),
                'urlAuthorize' => env('OAUTH_AUTHORITY') . env('OAUTH_AUTHORIZE_ENDPOINT'),
                'urlAccessToken' => env('OAUTH_AUTHORITY') . env('OAUTH_TOKEN_ENDPOINT'),
                'urlResourceOwnerDetails' => '',
                'scopes' => env('OAUTH_SCOPES'),
            ]);

            try {
                $newToken = $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => session('refresh_token'),
                ]);

                // Store the new values
                $this->storeTokens(
                    $newToken->getToken(),
                    $newToken->getRefreshToken(),
                    $newToken->getExpires()
                );

                return $newToken->getToken();
            } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return '';
            }
        } else {
            return session('access_token');
        }
    }
}
