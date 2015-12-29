<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Tests\VoloTestCase;

class RedirectTrailingSlashesControllerTest extends VoloTestCase
{
    public function testRedirectTrailingSlashAction()
    {
        $client = static::createClient();
        $path = '/city/berlin/';
        $target = '/city/berlin';

        $client->request('GET', $path);

        $this->assertEquals(
            Response::HTTP_MOVED_PERMANENTLY,
            $client->getResponse()->getStatusCode(),
            sprintf('status code should be "%s", got "%s" for "%s"', Response::HTTP_MOVED_PERMANENTLY, $client->getResponse()->getStatusCode(), $path)
        );

        $this->assertTrue(
            $client->getResponse()->isRedirect($target),
            sprintf('Location should be "%s", got "%s" for "%s"', $target, $client->getRequest()->headers->get('Location'), $path)
        );

        $client->followRedirect();
        $this->isSuccessful($client->getResponse());
    }
}
