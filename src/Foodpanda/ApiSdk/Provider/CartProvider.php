<?php

namespace Foodpanda\ApiSdk\Provider;

use GuzzleHttp\Exception\ClientException;

class CartProvider extends AbstractProvider
{
    /**
     * @param array $cart
     *
     * @return array
     */
    public function calculate(array $cart)
    {
        $request = $this->client->createRequest('POST', 'orders/calculate', ['body' => json_encode($cart)]);
        $request->addHeader('Content-type', 'application/json');

        $accessToken = $this->authenticator->authenticateClient();

        $this->client->attachAuthenticationDataToRequest($request, $accessToken);

        try {
            $data = $this->client->send($request)['data'];
        } catch (ClientException $e) {
            $data = (string) $e->getResponse()->getBody();
        }

        return json_decode($data, true);
    }
}
