<?php

namespace Foodpanda\ApiSdk\Api;

use CommerceGuys\Guzzle\Oauth2\AccessToken;

class OrderApiClient extends AbstractApiClient
{
    /**
     * @param string $jsonCart
     *
     * @return array
     */
    public function calculate($jsonCart)
    {
        $request = $this->client->createRequest(
            'POST',
            'orders/calculate',
            [
                'body' => $jsonCart
            ]
        );
        $request->addHeader('Content-type', 'application/json');

        $data = $this->authenticateClient();
        $accessToken = new AccessToken($data['access_token'], $data['token_type'], $data);
        $this->attachAuthenticationDataToRequest($request, $accessToken);

        return $this->send($request)['data'];
    }
}
