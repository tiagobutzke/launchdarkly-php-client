<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class CheckoutControllerTest extends VoloTestCase
{
    public function testGuest()
    {
        $client = static::createClient();

        $guestCode = 'm0zt';
        $client->request('GET', sprintf('/checkout/%s/delivery', $guestCode));

        $this->isSuccessful($client->getResponse());
    }

    public function testGuestPost()
    {
        $this->markTestIncomplete('TBD');
        $client = static::createClient();

        $guestCode = 'm0zt';
        $client->request('POST', sprintf('/checkout/%s/delivery', $guestCode));

        $this->isSuccessful($client->getResponse());
    }
}
