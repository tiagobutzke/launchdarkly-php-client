<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class ThumborControllerTest extends VoloTestCase
{
    public function testConfiguration()
    {
        $client = static::createClient();

        $client->request('GET', '/thumbor/configuration.js');

        $this->isSuccessful($client->getResponse());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/javascript'
            )
        );
    }

    public function testFake()
    {
        $client = static::createClient();

        $client->request('GET', '/thumbor/fake/a/b/c/d');

        $this->isSuccessful($client->getResponse());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'image/jpeg'
            )
        );
    }

    public function testFakeInProduction()
    {
        $client = static::createClient(['debug' => false]);

        $client->request('GET', '/thumbor/fake/a/b/c/d');

        $this->assertFalse($client->getResponse()->isSuccessful());
    }
}
