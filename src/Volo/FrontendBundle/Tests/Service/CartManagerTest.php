<?php

namespace Volo\FrontendBundle\Tests\Service;

use Foodpanda\ApiSdk\Entity\Cart\Cart;
use Foodpanda\ApiSdk\Entity\Cart\CartProduct;
use Foodpanda\ApiSdk\Entity\Cart\AreaLocation;
use Foodpanda\ApiSdk\Entity\Menu\Menu;
use Foodpanda\ApiSdk\Entity\MenuCategory\MenuCategory;
use Foodpanda\ApiSdk\Entity\Order\PostCalculateResponse;
use Foodpanda\ApiSdk\Entity\Product\Product;
use Foodpanda\ApiSdk\Provider\VendorProvider;
use Volo\FrontendBundle\Tests\VoloTestCase;

class CartManagerTest extends VoloTestCase
{
    public function testCalculate()
    {
        static::bootKernel();

        $vendorProvider = static::$kernel->getContainer()->get('volo_frontend.provider.vendor');
        $cartManager = static::$kernel->getContainer()->get('volo_frontend.service.cart_manager');

        $orderProduct = $this->getOderProduct($vendorProvider);

        $location = new AreaLocation();
        $location->setAreaId(127);

        $cart = new Cart();
        $cart->setExpeditionType('delivery');
        $cart->setLocation($location);
        $cart->getProducts()->add($orderProduct);

        $CalculatedCart = $cartManager->calculate($cart);

        $this->assertInstanceOf(PostCalculateResponse::class, $CalculatedCart);
    }

    /**
     * @param VendorProvider $vendorProvider
     *
     * @return CartProduct
     */
    protected function getOderProduct(VendorProvider $vendorProvider)
    {
        $vendor = $vendorProvider->find(684);

        /** @var Menu $menu */
        $menu = $vendor->getMenus()->first();
        /** @var MenuCategory $category */
        $category = $menu->getMenuCategories()->first();
        /** @var Product $product */
        $product = $category->getProducts()->first();

        $orderProduct = new CartProduct();
        $orderProduct->setVendorId($vendor->getId());
        $orderProduct->setVariationId($product->getProductVariations()->first()->getId());
        $orderProduct->setQuantity(1);

        return $orderProduct;
    }
}
