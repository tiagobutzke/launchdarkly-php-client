<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Tests\VoloTestCase;

class VendorControllerTest extends VoloTestCase
{
    public function testRestaurants()
    {
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');

        return;

        $client = static::createClient();

        $client->request('GET', '/restaurant/s9iz/la-piccola');

        $this->isSuccessful($client->getResponse());
    }

    public function testVendorByCode()
    {
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');

        return;

        $client = static::createClient();

        $path = '/restaurant/s9iz';
        $client->request('GET', $path);

        $target = $client->getRequest()->getSchemeAndHttpHost() . '/restaurant/s9iz/la-piccola';

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
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');

        return;

        $client = static::createClient();

        $path = '/restaurant/la-piccola';

        $client->request('GET', $path);
        $target = $client->getRequest()->getSchemeAndHttpHost() . '/restaurant/s9iz/la-piccola';

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
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');

        return;

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
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');
        return;

        $client = static::createClient();

        $client->request('GET', '/restaurant/a1bc');

        $this->isSuccessful($client->getResponse(), false);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testVendorByUpperCaseCodeWithoutUrlKey()
    {
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');
        return;

        $client = static::createClient();

        $client->request('GET', '/restaurant/S9Iz');

        $this->assertTrue($client->getResponse()->isRedirect($client->getRequest()->getSchemeAndHttpHost() . '/restaurant/s9iz/la-piccola'));
    }

    public function testVendorByUpperCaseUrlKey()
    {
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');

        return;

        $client = static::createClient();

        $client->request('GET', '/restaurant/LA-PICCOLA');

        $this->assertTrue($client->getResponse()->isRedirect($client->getRequest()->getSchemeAndHttpHost() . '/restaurant/s9iz/la-piccola'));
    }

    public function testVendorPageSeoIndexFollow()
    {
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');

        return;

        $client = static::createClient();

        $client->request('GET', '/restaurant/s9iz/la-piccola');

        $response = $client->getResponse();

        $this->assertContains('index, follow', $response->getContent());
    }

    public function testVendorByUpperCaseCodeWithUrlKey()
    {
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');

        return;

        $client = static::createClient();

        $client->request('GET', '/restaurant/S9IZ/la-piccola');

        $this->assertTrue($client->getResponse()->isRedirect($client->getRequest()->getSchemeAndHttpHost() . '/restaurant/s9iz/la-piccola'));
    }

    public function testVendorByUpperCaseUrlKeyAndCode()
    {
        $this->markTestSkipped('Temporarily skipped to deploy flood feature v2 (INTVOLO-1798)');

        return;

        $client = static::createClient();

        $client->request('GET', '/restaurant/s9iz/LA-PICCOLA');

        $this->assertTrue($client->getResponse()->isRedirect($client->getRequest()->getSchemeAndHttpHost() . '/restaurant/s9iz/la-piccola'));
    }
}
