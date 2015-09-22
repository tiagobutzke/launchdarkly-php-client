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

    public function testCmsUppercaseAction()
    {
        $client = static::createClient();

        $client->request('GET', '/contents/PRIVACY.htm');

        $this->assertTrue($client->getResponse()->isRedirect($client->getRequest()->getSchemeAndHttpHost() . '/contents/privacy.htm'));
    }

    public function testPrivacyPath()
    {
        $client = static::createClient();
        $client->followRedirects(false);

        $client->request('GET', '/contents/privacy.htm');

        $this->isSuccessful($client->getResponse());
    }

    public function testunknownCms()
    {
        $client = static::createClient();
        $client->followRedirects(false);

        $client->request('GET', '/contents/unknown.htm');

        $this->assertTrue($client->getResponse()->isNotFound());
    }
}
