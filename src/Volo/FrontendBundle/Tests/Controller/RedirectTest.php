<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Tests\VoloTestCase;

class RedirectTest extends VoloTestCase
{
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
    }

    public function testLocation()
    {
        $path = '/location/la-piccola';
        $target = '/restaurant/s9iz/la-piccola';

        $client = static::createClient();

        $client->request('GET', $path);

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
    public function urlDataProvider()
    {
        return [
            ['/terms', 'http://localhost/contents/terms-and-conditions.htm'],
            ['/drivers', 'http://localhost/contents/drivers-and-restaurants.htm'],
            ['/restaurants', 'http://localhost/contents/drivers-and-restaurants.htm'],
            ['/imprint', 'http://localhost/contents/imprint.htm'],
            ['/r/berlin', 'http://localhost/city/berlin'],
            ['/signup', 'http://localhost/login'],
        ];
    }
}
