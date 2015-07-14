<?php

namespace Volo\FrontendBundle\Tests\Service;

use ReflectionClass;
use Volo\FrontendBundle\Service\CartManagerService;
use Volo\FrontendBundle\Tests\VoloTestCase;

class CartManagerTest extends VoloTestCase
{
    /**
     * @dataProvider repopulateSpecialInstructionsDataProvider
     */
    public function testRepopulateSpecialInstructions($data, $response)
    {
        $cartManager = $this->getMockBuilder(CartManagerService::class)->disableOriginalConstructor()->getMock();
        
        $class = new ReflectionClass(CartManagerService::class);
        $method = $class->getMethod('repopulateSpecialInstructions');
        $method->setAccessible(true);
        
        $result = $method->invokeArgs($cartManager, [$data, $response]);
        
        $this->assertEquals($result['vendorCart'][0]['products'], $data['products']);
    }

    public function repopulateSpecialInstructionsDataProvider()
    {
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $products = [];

            foreach (range(1, rand(0, 10)) as $tmp) {
                $randomStr = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, rand(0, 10));

                $toppings = [];
                foreach (range(0, rand(0, 5)) as $tmp2) {
                    $toppings[] = ['id' => rand(0, 10)];
                }
                
                $productId = rand(0, 100);
                $products[] = [
                    'variation_id'         => $productId,
                    'product_variation_id' => $productId,
                    'special_instructions' => $randomStr,
                    'quantity'             => rand(1, 10),
                    'toppings'             => $toppings,
                ];
            }
            
            $response = $products;
            foreach ($response as &$product) {
                unset($product['special_instructions']);
            }
            
            $data[] = [['products' => $products], ['vendorCart' => [['products' => $response]]]];
        }

        return $data;
    }
}
