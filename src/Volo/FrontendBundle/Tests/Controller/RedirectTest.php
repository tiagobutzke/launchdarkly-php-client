<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Tests\VoloTestCase;

class RedirectTest extends VoloTestCase
{
    /**
     * @dataProvider cmsDataProvider
     *
     * @param string $path
     * @param string $target
     */
    public function testCms($path, $target)
    {
        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY,
            $client->getResponse()->getStatusCode(),
            sprintf('status code should be "%s", got "%s" for "%s"', Response::HTTP_MOVED_PERMANENTLY, $client->getResponse()->getStatusCode(), $path)
        );

        $this->assertTrue(
            $client->getResponse()->isRedirect($target),
            sprintf('Location should be "%s", got "%s" for "%s"', $target, $client->getRequest()->headers->get('Location'), $path)
        );
    }

    /**
     * @dataProvider urlDataProvider
     *
     * @param string $path
     * @param string $target
     */
    public function testRestaurants($path, $target)
    {
        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY,
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

    public function testLocation()
    {
        $client = static::createClient();

        $path = '/location/la-piccola';
        $client->request('GET', $path);

        $target = $client->getRequest()->getSchemeAndHttpHost() . '/restaurant/s9iz/la-piccola';

        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY,
            $client->getResponse()->getStatusCode(),
            sprintf('status code should be "%s", got "%s" for "%s"', Response::HTTP_MOVED_PERMANENTLY, $client->getResponse()->getStatusCode(), $path)
        );

        $client->followRedirect();
        $this->assertTrue(
            $client->getResponse()->isRedirect($target),
            sprintf('Location should be "%s", got "%s" for "%s"', $target, $client->getRequest()->headers->get('Location'), $path)
        );
    }

    /**
     * @return array
     */
    public function cmsDataProvider()
    {
        return [
            ['/terms', 'http://localhost/contents/terms-and-conditions.htm'],
            ['/drivers', 'http://localhost/contents/drivers-and-restaurants.htm'],
            ['/restaurants', 'http://localhost/contents/drivers-and-restaurants.htm'],
            ['/imprint', 'http://localhost/contents/imprint.htm'],
            ['/signup', 'http://localhost/login'],
        ];
    }
    /**
     * @return array
     */
    public function urlDataProvider()
    {
        return [
            ['/r/berlin', 'http://localhost/city/berlin'],
            ['/r/muenchen', 'http://localhost/city/muenchen'],
            ['/r/koeln', 'http://localhost/city/koeln'],
            ['/r/duesseldorf', 'http://localhost/city/duesseldorf'],

            ['/city/munchen', 'http://localhost/city/muenchen'],
            ['/city/koln', 'http://localhost/city/koeln'],
            ['/city/dusseldorf', 'http://localhost/city/duesseldorf'],
        ];
    }
}
