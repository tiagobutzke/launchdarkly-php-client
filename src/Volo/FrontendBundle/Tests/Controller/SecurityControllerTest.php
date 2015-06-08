<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Tests\VoloTestCase;

class SecurityControllerTestCase extends VoloTestCase
{
    public function testSuccessfulLogin()
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/login', [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('POST', '/login_check', [
            '_username' => 'john.doe@rocket-internet.de',
            '_password' => 'good',
        ]);

        $this->isSuccessful($client->getResponse());
        $this->assertInstanceOf(
            'Volo\FrontendBundle\Security\Token',
            $client->getContainer()->get('security.token_storage')->getToken()
        );
    }

    public function testFailedLogin()
    {
        $client = static::createClient();

        $client->request('POST', '/login_check', [
            '_username' => 'john.doe@rocket-internet.de',
            '_password' => 'bad',
        ], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $this->assertEquals(Response::HTTP_FOUND,
            $client->getResponse()->getStatusCode(),
            sprintf('status code should be "%s", got "%s" for "%s"', Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'http://localhost/login_check')
        );

        $this->assertTrue(
            $client->getResponse()->isRedirect('http://localhost/login'),
            sprintf('Location should be "%s", got "%s" for "%s"', 'http://localhost/login', $client->getRequest()->headers->get('Location'), 'http://localhost/login_check')
        );

        $client->followRedirect();

        $this->isSuccessful($client->getResponse(), false);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

        $this->assertInstanceOf(
            'Symfony\Component\Security\Core\Authentication\Token\AnonymousToken',
            $client->getContainer()->get('security.token_storage')->getToken()
        );
    }
}
