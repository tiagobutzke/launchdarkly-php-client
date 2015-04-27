<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class MenuControllerTest extends VoloTestCase
{
    public function testMenu()
    {
        $client = static::createClient();

        $client->request('GET', '/menu');

        $this->isSuccessful($client->getResponse());
    }
}
