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
        $cart = ['products' => ['fake'], 'expeditionType' => 'delivery', 'order_time' => date('c')];
        
        $sessionId = 'test_session_id';
        $client->getContainer()->get('session')->setId($sessionId);
        $client->getContainer()->get('volo_frontend.service.cart_manager')->saveCart($sessionId, $vendorId, $cart);
        
        $client->request('GET', sprintf('/checkout/%s/delivery', $vendorCode));

        $this->isSuccessful($client->getResponse());
    }

    public function testGuestPost()
    {
        $this->markTestIncomplete('TBD');
        $client = static::createClient();

        $guestCode = 'm2hc';
        $client->getContainer()->get('volo_frontend.service.cart_manager')->saveCart('test', 'm0zt', array());

        $client->request('POST', sprintf('/checkout/%s/delivery', $guestCode));

        $this->isSuccessful($client->getResponse());
    }
}
