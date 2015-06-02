<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Tests\VoloTestCase;

class CartControllerTest extends VoloTestCase
{
    public function testCalculate()
    {
        $client = static::createClient();

        $body = [
            'expeditionType' => 'delivery',
            'vouchers' => [],
            'vendor_id' => 9,
            'products' => [
                [
                    'vendor_id' => 9,
                    'variation_id' => 2179465,
                    'quantity' => 2,
                    'groupOrderUserName' => '',
                    'toppings' => [
                        [
                            'id' => 206892,
                            'type' => 'full',
                        ],
                        [
                            'id' => 206896,
                            'type' => 'full',
                        ],
                    ],
                    'choices' => [],
                ]
            ],
            'location' => [
                'location_type' => 'polygon',
                'latitude' => 52.5237282,
                'longitude"' => 13.3908286
            ],
            'orderTime' => '2015-05-12T07:31:20.795Z',
            'paymentTypeId' => 0,
            'activeLanguage' => 1,
            'groupCode' => '',
            'groupOrderVersion' => 0,
            'orderComment' => '',
            'vendorPickupLocationId' => 0,
            'deliveryTimeMode' => ''
        ];
        $client->request('POST', '/cart/calculate', [], [], [], json_encode($body));

        $this->isSuccessful($client->getResponse());
    }

    public function testCalculateEmptyBody()
    {
        $client = static::createClient();

        $body = '';
        $client->request('POST', '/cart/calculate', [], [], [], $body);

        $this->isSuccessful($client->getResponse(), false);
    }

    public function testCalculateMalformedJson()
    {
        $client = static::createClient();

        $body = 'foo bar';
        $client->request('POST', '/cart/calculate', [], [], [], $body);

        $this->isSuccessful($client->getResponse(), false);
    }

    public function testCalculateWithBadRequest()
    {
        $client = static::createClient();

        $body = [
            'expeditionType' => 'delivery',
            'vouchers' => [],
            'products' => [],
            'location' => [
                'location_type' => 'area',
                'area_id' => 0
            ],
            'orderTime' => '',
            'paymentTypeId' => 0,
            'activeLanguage' => 1,
            'groupCode' => '',
            'groupOrderVersion' => 0,
            'orderComment' => '',
            'vendorPickupLocationId' => 0,
            'deliveryTimeMode' => ''
        ];
        $client->request('POST', '/cart/calculate', [], [], [], json_encode($body));

        $this->isSuccessful($client->getResponse(), false);
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $client->getResponse()->getStatusCode());
    }

    public function testCalculateWithWrongData()
    {
        $client = static::createClient();

        $body = [
            'expeditionType' => 'random string',
            'vouchers' => [],
            'products' => [],
            'location' => [
                'location_type' => 'area',
                'area_id' => 127
            ],
            'orderTime' => '',
            'paymentTypeId' => 0,
            'activeLanguage' => 1,
            'groupCode' => '',
            'groupOrderVersion' => 0,
            'orderComment' => '',
            'vendorPickupLocationId' => 0,
            'deliveryTimeMode' => ''
        ];
        $client->request('POST', '/cart/calculate', [], [], [], json_encode($body));

        $response = $client->getResponse();
        $this->isSuccessful($response, false);

        $this->assertEquals(400, $response->getStatusCode(), $this->formatErrorMessage($response, 'The status code should be 400'));

        $content = $response->getContent();
        $this->assertJson($content);
        $errorMessage = json_decode($content, true);

        $this->assertArrayHasKey('error', $errorMessage);
        $this->assertArrayHasKey('code', $errorMessage['error']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $errorMessage['error']['code']);

        $this->assertTrue(is_array($errorMessage['error']['errors']), 'The response should contain an array of errors');
        $this->assertEquals(1, count($errorMessage['error']['errors']));

        $firstErrorMessage = $errorMessage['error']['errors'][0];
        $this->assertTrue(is_array($firstErrorMessage), 'The response should contain an error object');
        $this->assertArrayHasKey('field_name', $firstErrorMessage);
        $this->assertEquals('expedition_type', $firstErrorMessage['field_name']);
        $this->assertArrayHasKey('violation_messages', $firstErrorMessage);

        $this->assertTrue(is_array($firstErrorMessage['violation_messages']));
        $this->assertEquals('This value is not a valid option', $firstErrorMessage['violation_messages'][0]);
    }
}
