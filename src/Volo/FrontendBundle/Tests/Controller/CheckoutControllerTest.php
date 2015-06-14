<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class CheckoutControllerTest extends VoloTestCase
{
    public function testGuest()
    {
        $client = static::createClient();

        $vendorId = '684';
        $vendorCode = 'm2hc';
        $cart = ['products' => ['fake'], 'expeditionType' => 'delivery', 'orderTime' => date('c')];
        
        $sessionId = 'test_session_id';
        $client->getContainer()->get('session')->setId($sessionId);
        $client->getContainer()->get('volo_frontend.service.cart_manager')->saveCart($sessionId, $vendorId, $this->getCart());
        
        $client->request('GET', sprintf('/checkout/%s/delivery', $vendorCode));

        $this->isSuccessful($client->getResponse());
    }

    public function testGuestPost()
    {
        $this->markTestIncomplete('TBD');
        $client = static::createClient();

        $guestCode = 'm2hc';
        $client->getContainer()->get('volo_frontend.service.cart_manager')->saveCart('test', 'm0zt', $this->getCart());

        $client->request('POST', sprintf('/checkout/%s/delivery', $guestCode));

        $this->isSuccessful($client->getResponse());
    }

    /**
     * @return array
     */
    public function getCart()
    {
        return [
            'expeditionType' => 'delivery',
            'vouchers' => [],
            'vendor_id' => 684,
            'products' => [
                [
                    'vendor_id' => 684,
                    'variation_id' => 2179465,
                    'quantity' => 2,
                    'groupOrderUserName' => '',
                    'toppings' => [
                        [
                            'id' => 206892,
                            'type' => 'full',
                        ],
                        [
                            'id' => 206896,
                            'type' => 'full',
                        ],
                    ],
                    'choices' => [],
                ]
            ],
            'location' => [
                'location_type' => 'polygon',
                'latitude' => 52.5237282,
                'longitude"' => 13.3908286
            ],
            'orderTime' => '2015-05-12T07:31:20.795Z',
            'paymentTypeId' => 0,
            'activeLanguage' => 1,
            'groupCode' => '',
            'groupOrderVersion' => 0,
            'orderComment' => '',
            'vendorPickupLocationId' => 0,
            'deliveryTimeMode' => ''
        ];
    }
}
