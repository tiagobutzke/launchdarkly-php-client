<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Tests\VoloTestCase;

class VendorControllerTest extends VoloTestCase
{
    public function testRestaurants()
    {
        $client = static::createClient();

        $client->request('GET', '/restaurant/s9iz/la-piccola');

        $this->isSuccessful($client->getResponse());
    }

    public function testVendorByCode()
    {
        $path = '/restaurant/s9iz';
        $target = '/restaurant/s9iz/la-piccola';

        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertEquals(Response::HTTP_FOUND,
            $client->getResponse()->getStatusCode(),
            sprintf('status code should be "%s", got "%s" for "%s"', Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), $path)
        );

        $this->assertTrue(
            $client->getResponse()->isRedirect($target),
            sprintf('Location should be "%s", got "%s" for "%s"', $target, $client->getRequest()->headers->get('Location'), $path)
        );
        $client->followRedirect();

        $this->isSuccessful($client->getResponse());
    }

    public function testVendorByUrlKey()
    {
        $path = '/restaurant/la-piccola';
        $target = '/restaurant/s9iz/la-piccola';

        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertEquals(
            Response::HTTP_FOUND,
            $client->getResponse()->getStatusCode(),
            sprintf(
                'status code should be "%s", got "%s" for "%s"',
                Response::HTTP_FOUND,
                $client->getResponse()->getStatusCode(),
                $path
            )
        );

        $this->assertTrue(
            $client->getResponse()->isRedirect($target),
            sprintf(
                'Location should be "%s", got "%s" for "%s"',
                $target,
                $client->getRequest()->headers->get('Location'),
                $path
            )
        );
        $client->followRedirect();

        $this->isSuccessful($client->getResponse());
    }

    public function testVendorByUrlKeyWithWrongUrlKey()
    {
        $path = '/restaurant/foo-bar';

        $client = static::createClient();

        $client->request('GET', $path);

        $this->isSuccessful($client->getResponse(), false);
        $this->assertEquals(
            Response::HTTP_NOT_FOUND,
            $client->getResponse()->getStatusCode(),
            sprintf(
                'status code should be "%s", got "%s" for "%s"',
                Response::HTTP_NOT_FOUND,
                $client->getResponse()->getStatusCode(),
                $path
            )
        );
    }

    public function testVendorByCodeWithWrongVendorCode()
    {
        $client = static::createClient();

        $client->request('GET', '/restaurant/a1bc');

        $this->isSuccessful($client->getResponse(), false);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
}
