<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class LocationControllerTest extends VoloTestCase
{
    public function testRestaurants()
    {
        $client = static::createClient();

        $client->request('GET', '/city/5');

        $this->isSuccessful($client->getResponse());
    }
}
