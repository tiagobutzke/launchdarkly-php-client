<?php

use Foodpanda\ApiSdk\Api\FoodpandaClient;
use GuzzleHttp\Ring\Client\CurlHandler;

$handler = new CurlHandler();

return [
    'client' => [
        'className' => FoodpandaClient::class,
        'config' => [
            'base_url' => 'http://front.fp/api/v4/',
            'handler' => $handler,
        ],
    ],
];
