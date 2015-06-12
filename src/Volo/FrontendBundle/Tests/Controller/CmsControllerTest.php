<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Tests\VoloTestCase;

class CmsControllerTest extends VoloTestCase
{
    public function testCmsAction()
    {
        $client = static::createClient();

        $client->request('GET', '/contents/privacy.htm');

        $this->isSuccessful($client->getResponse());
    }

    public function testPrivacyPath()
    {
        $client = static::createClient();
        $client->followRedirects(false);

        $client->request('GET', '/privacy');

        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY, $client->getResponse()->getStatusCode());

        $client->followRedirect();
        $this->isSuccessful($client->getResponse());
    }
}
