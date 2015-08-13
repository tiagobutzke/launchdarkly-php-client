<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class LocationControllerTest extends VoloTestCase
{
    public function testRestaurants()
    {
        $client = static::createClient();

        $client->request('GET', '/city/berlin');

        $this->isSuccessful($client->getResponse());
    }

    public function testRedirectionCityUppercaseUrlKey()
    {
        $client = static::createClient();

        $client->request('GET', '/city/BERLIN');

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirect('/city/berlin'));
    }

    public function testCityPageSeoIndexFollow()
    {
        $client = static::createClient();

        $client->request('GET', '/city/berlin');

        $response = $client->getResponse();

        $this->assertContains('index, follow', $response->getContent());
    }

    public function testRedirectionCityMixedCaseUrlKey()
    {
        $client = static::createClient();

        $client->request('GET', '/city/BeRlIn');

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirect('/city/berlin'));
    }
}
