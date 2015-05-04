<?php

namespace Foodpanda\ApiSdk\Provider;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\Order\GuestCustomer;

class CustomerProvider extends AbstractProvider
{
    /**
     * @param AccessToken $token
     *
     * @return Customer
     */
    public function getCustomer(AccessToken $token)
    {
        // TODO: add "include: $withAddress" parameter in the request
        $request = $this->client->createRequest('GET', 'customers');

        $this->client->attachAuthenticationDataToRequest($request, $token);

        $data =  $this->client->send($request)['data'];

        return $this->serializer->denormalizeCustomer($data);
    }

    /**
     * @param AccessToken $token
     *
     * @return array
     */
    public function getCustomerWithoutAddresses(AccessToken $token)
    {
        // TODO: add "include: $withAddress" parameter in the request
        $request = $this->client->createRequest('GET', 'customers');

        $this->client->attachAuthenticationDataToRequest($request, $token);

        $data =  $this->client->send($request)['data'];

        return $this->serializer->denormalizeCustomer($data);
    }

    /**
     * @param GuestCustomer $guestCustomer
     * @param int $languageId
     *
     * @return GuestCustomer
     */
    public function create(GuestCustomer $guestCustomer, $languageId = 1)
    {
        $guestCustomerJson = $this->serializer->serialize($guestCustomer, 'json');

        $request = $this->client->createRequest(
            'POST',
            sprintf('customers/create_guest?language_id=%d', $languageId),
            [
                'body' => $guestCustomerJson
            ]
        );

        $request->addHeader('Content-type', 'application/json');

        $accessToken = $this->authenticator->authenticateClient();
        $this->client->attachAuthenticationDataToRequest($request, $accessToken);
        $data =  $this->client->send($request)['data'];

        return $this->serializer->denormalizeGuestCustomer($data);
    }
}
