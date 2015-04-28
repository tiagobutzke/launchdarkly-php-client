<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Entity\Cart\Cart;
use Foodpanda\ApiSdk\Entity\Order\PostCalculateResponse;

class CartProvider extends AbstractProvider
{
    /**
     * @param Cart $cart
     *
     * @return PostCalculateResponse
     */
    public function calculate(Cart $cart)
    {
        $json = $this->serializer->serialize($cart, 'json');

        $request = $this->client->createRequest(
            'POST',
            'orders/calculate',
            [
                'body' => $json
            ]
        );
        $request->addHeader('Content-type', 'application/json');

        $accessToken = $this->authenticator->authenticateClient();

        $this->client->attachAuthenticationDataToRequest($request, $accessToken);

        $response = $this->client->send($request)['data'];

        return $this->serializer->denormalizePostCalculateReponse($response);
    }
}
