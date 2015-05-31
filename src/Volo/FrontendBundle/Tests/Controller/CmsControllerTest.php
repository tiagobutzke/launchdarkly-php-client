<?php

namespace Volo\FrontendBundle\Tests\Controller;

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

        $client->request('GET', '/privacy');

        $this->isSuccessful($client->getResponse());
    }
}
