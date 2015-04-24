<?php

namespace Foodpanda\ApiSdk\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Ring\Client\MockHandler;

class ApiSdkTestSuite extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Client
     */
    protected function getClient()
    {
        $mock = new MockHandler(new MockHandlerCallable());

        return new Client(['handler' => $mock]);
    }
}
