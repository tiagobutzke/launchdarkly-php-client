<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class CustomerControllerTest extends VoloTestCase
{
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
                'first_name' => 'first',
                'last_name' => 'last',
                'password' => 'password',
                'email' => 'test@domain.com',
                'mobile_country_code' => '+49',
                'mobile_number' => '12345678',
            ]
        ];
        $client->request('POST', '/customer', $params);

        $this->isSuccessful($client->getResponse());
    }
}
