<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class SecurityControllerTestCase extends VoloTestCase
{
    public function testSuccessfulLogin()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/login');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $form              = $crawler->selectButton('login')->form();
        $form['_username'] = 'john.doe@rocket-internet.de';
        $form['_password'] = 'good';
        $client->submit($form);

        $this->isSuccessful($client->getResponse());
        $this->assertInstanceOf('Volo\FrontendBundle\Security\Token',
            $client->getContainer()->get('security.token_storage')->getToken()
        );
    }

    public function testFailedLogin()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/login');

        $this->isSuccessful($client->getResponse());

        $form              = $crawler->selectButton('login')->form();
        $form['_username'] = 'john.doe@rocket-internet.de';
        $form['_password'] = 'bad';
        $client->submit($form);

        $this->isSuccessful($client->getResponse());
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authentication\Token\AnonymousToken',
            $client->getContainer()->get('security.token_storage')->getToken()
        );
    }
}
