<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class VendorControllerTest extends VoloTestCase
{
    public function testRestaurants()
    {
        $client = static::createClient();

        $client->request('GET', '/vendor/690');

        $this->isSuccessful($client->getResponse());
    }
}
