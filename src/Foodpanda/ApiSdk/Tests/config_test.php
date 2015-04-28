<?php

use Foodpanda\ApiSdk\Api\FoodpandaClient;
use Foodpanda\ApiSdk\Tests\MockHandlerCallable;
use GuzzleHttp\Ring\Client\MockHandler;

$handler = new MockHandler(new MockHandlerCallable());

return [
    'client' => [
        'className' => FoodpandaClient::class,
        'config' => [
            'base_url' => 'http://front.fp/api/v4/',
            'handler' => $handler,
        ],
    ],
];
