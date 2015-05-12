<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class VendorControllerTest extends VoloTestCase
{
    public function testRestaurants()
    {
        $client = static::createClient();

        $client->request('GET', '/restaurant/m0zt/royal-garden-restaurant');

        $this->isSuccessful($client->getResponse());
    }
}
