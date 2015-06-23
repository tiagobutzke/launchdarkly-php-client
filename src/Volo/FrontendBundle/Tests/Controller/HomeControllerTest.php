<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class HomeControllerTest extends VoloTestCase
{
    public function testHome()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->isSuccessful($client->getResponse());
    }

    public function testFilterPostalCode()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->isSuccessful($client->getResponse());
    }
}
