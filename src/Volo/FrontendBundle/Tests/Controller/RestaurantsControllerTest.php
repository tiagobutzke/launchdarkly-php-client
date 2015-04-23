<?php

namespace Volo\FrontendBundle\Tests\Controller;

class RestaurantsControllerTest extends VoloTestCase
{
    public function testRestaurants()
    {
        $client = static::createClient();

        $client->request('GET', '/restaurants');

        $this->isSuccessful($client->getResponse());
    }
}
