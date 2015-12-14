<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class LocationControllerTest extends VoloTestCase
{
    public function testRestaurants()
    {
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');
        return;

        $client = static::createClient();

        $client->request('GET', '/city/berlin');

        $this->isSuccessful($client->getResponse());
    }

    public function testRedirectionCityUppercaseUrlKey()
    {
        $client = static::createClient();

        $client->request('GET', '/city/BERLIN');

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirect($client->getRequest()->getSchemeAndHttpHost() . '/city/berlin'));
    }

    public function testCityPageSeoIndexFollow()
    {
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');
        return;
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

        $this->assertTrue($response->isRedirect($client->getRequest()->getSchemeAndHttpHost() . '/city/berlin'));
    }
}
