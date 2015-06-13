<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Volo\FrontendBundle\Tests\VoloTestCase;

class OrderControllerTest extends VoloTestCase
{
    public function testStatusAction()
    {
        $client = static::createClient();

        $client->request('GET', '/orders/s9iz-q2cg/tracking');

        $this->isSuccessful($client->getResponse());
    }
}
