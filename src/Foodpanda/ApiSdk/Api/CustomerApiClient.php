<?php

namespace Foodpanda\ApiSdk\Api;

use CommerceGuys\Guzzle\Oauth2\AccessToken;

class CustomerApiClient extends AbstractApiClient
{
    /**
     * @param AccessToken $token
     * @param bool $withAddress
     *
     * @return array
     */
    public function getCustomers(AccessToken $token, $withAddress)
    {
        // TODO: add "include: $withAddress" parameter in the request
        $request = $this->client->createRequest('GET', 'customers');

        $this->attachAuthenticationDataToRequest($request, $token);

        return $this->send($request)['data'];
    }

    /**
     * @param string $guestCustomerJson
     * @param int $languageId
     *
     * @return mixed
     */
    public function createGuestCustomer($guestCustomerJson, $languageId)
    {
        $request = $this->client->createRequest(
            'POST',
            sprintf('customers/create_guest?language_id=%d', $languageId),
            [
                'body' => $guestCustomerJson
            ]
        );

        $request->addHeader('Content-type', 'application/json');

        $data = $this->authenticateClient();
        $accessToken = new AccessToken($data['access_token'], $data['token_type'], $data);
        $this->attachAuthenticationDataToRequest($request, $accessToken);

        return $this->send($request)['data'];
    }
}
