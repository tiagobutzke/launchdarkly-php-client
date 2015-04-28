<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Api\OrderApiClient;
use Foodpanda\ApiSdk\Entity\Cart\Cart;
use Foodpanda\ApiSdk\Entity\Order\PostCalculateResponse;

class CartProvider extends AbstractProvider
{
    /**
     * @var OrderApiClient
     */
    protected $client;

    /**
     * @param Cart $cart
     *
     * @return PostCalculateResponse
     */
    public function calculate(Cart $cart)
    {
        $jsonCart = $this->serializer->serialize($cart, 'json');

        $response = $this->client->calculate($jsonCart);

        return $this->serializer->denormalizePostCalculateReponse($response);
    }
}
