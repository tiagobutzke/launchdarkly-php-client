<?php

namespace Volo\FrontendBundle\Tests\Controller;

class SecurityControllerTest extends BaseControllerTest
{
    public function testSuccessfulLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $form              = $crawler->selectButton('login')->form();
        $form['_username'] = 'john.doe@rocket-internet.de';
        $form['_password'] = 'good';
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInstanceOf('Volo\FrontendBundle\Security\Token',
            $this->client->getContainer()->get('security.token_storage')->getToken()
        );
    }

    public function testFailedLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $form              = $crawler->selectButton('login')->form();
        $form['_username'] = 'john.doe@rocket-internet.de';
        $form['_password'] = 'bad';
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authentication\Token\AnonymousToken',
            $this->client->getContainer()->get('security.token_storage')->getToken()
        );
    }
}
