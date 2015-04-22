<?php

namespace Volo\FrontendBundle\Tests\Controller;

class HomeControllerTestCase extends VoloTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->isSuccessful($client->getResponse());
    }
}
