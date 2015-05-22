<?php

namespace Volo\FrontendBundle\Tests\Controller;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Volo\FrontendBundle\Security\Token;
use Volo\FrontendBundle\Tests\VoloTestCase;

class CustomerControllerTest extends VoloTestCase
{
    const ACCESS_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpZCI6IjYyYmI3ZTY3ZjMyYzVmNTNjNTVlOGY0NDIyMzQ4MDE2ZGMxZjUxNjAiLCJjbGllbnRfaWQiOiJIVE1MNSIsInVzZXJfaWQiOiJtYXhpbWUuZ2F1ZHJvbkByb2NrZXQtaW50ZXJuZXQuZGUiLCJleHBpcmVzIjoxNDYwNjQ5MjE5LCJ0b2tlbl90eXBlIjoiYmVhcmVyIiwic2NvcGUiOiJBUElfQ1VTVE9NRVIifQ.LAP1yb37O7JLf5Td8QtLqD9bH9yW5FuQ-bd_EAgPM-KyMdJJLNAYM2v-J4R0tYpkjHW0tLiFkQ4RxnXqB2mVLseUv_YE1qZwWUndzx3qss9Rl5PCHQ3THBhsdgQYsyN7LW7cEVIyzj_e8S3K4pckO5CC-HDvUjQz_F4ksNte2AVE3wr_ATOO1hCpoL3hS2a-An3YJHKLWA2ky3a-EM0ztxLMR1SSh9Gv3k8rRP1vtgZ0jMK3CUWcs0LgkkbYbGN-_yfNObQWX4f-rtEkeUepbeW3t0jj-Ie_05v6YmOonoBZMgJplSRbEJ0uUVAgWHUJ8Ed6yVk237XxoYVJraTCcw';

    public function testCreateNewCustomer()
    {
        $client = static::createClient();

        $client->request('GET', '/customer');

        $this->isSuccessful($client->getResponse());
    }

    public function testSaveNewCustomer()
    {
        $client = static::createClient();

        $params = [
            'customer' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'password' => 'good',
                'email' => 'john.doe@rocket-internet.de',
                'mobile_number' => '017673412345',
            ]
        ];
        $client->request('POST', '/customer', $params);

        $this->assertTrue($client->getResponse()->isRedirect());

        $token = $client->getContainer()->get('security.token_storage')->getToken();
        $this->assertInstanceOf(Token::class, $token);

        $customer = $token->getAttribute('customer');
        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals($params['customer']['email'], $customer->getEmail());
        $this->assertEquals($params['customer']['first_name'], $customer->getFirstName());
        $this->assertEquals($params['customer']['last_name'], $customer->getLastName());
        /** @var AccessToken $accessToken */
        $accessToken = $token->getAttribute('tokens');
        $this->assertInstanceOf(AccessToken::class, $accessToken);
        $this->assertArrayHasKey('access_token', $accessToken->getData());
        $this->assertEquals(self::ACCESS_TOKEN, $accessToken->getData()['access_token']);
    }
}
