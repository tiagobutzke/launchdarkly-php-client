<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Tests\VoloTestCase;

class ProfileControllerTest extends VoloTestCase
{
    public function testIndexAsLoggedInUser()
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

        $client->request('GET', '/profile');

        $this->isSuccessful($client->getResponse());
    }

    public function testIndexAsAnonymous()
    {
        $client = static::createClient();

        $client->request('GET', '/profile');

        $this->isSuccessful($client->getResponse(), false);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testUpdatePasswordAsAnonymous()
    {
        $client = static::createClient();

        $client->request('POST', '/profile/update_password', [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $this->isSuccessful($client->getResponse(), false);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}
