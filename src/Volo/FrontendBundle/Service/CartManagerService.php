<?php

namespace Volo\FrontendBundle\Service;

use Doctrine\Common\Cache\Cache;
use Foodpanda\ApiSdk\Provider\CartProvider;
use Foodpanda\ApiSdk\Provider\VendorProvider;

class CartManagerService
{
    /**
     * @var Cache
     */
    protected $cache;

    const CARTS_KEY_PREFIX = 'customer_carts:';
    const CART_KEY = 'cart';
    const DEFAULT_CART_FLAG = 'default';

    /**
     * @var CartProvider;
     */
    protected $cartProvider;

    /**
     * @var VendorProvider;
     */
    protected $vendorProvider;

    /**
     * @param Cache          $cache
     * @param CartProvider   $cartProvider
     * @param VendorProvider $vendorProvider
     */
    public function __construct(
        Cache $cache,
        CartProvider $cartProvider,
        VendorProvider $vendorProvider
    ) {
        $this->cartProvider = $cartProvider;
        $this->vendorProvider = $vendorProvider;
        $this->cache = $cache;
    }

    /**
     * @param string $sessionId
     * @param string $vendorIdentifier
     * @param array $cart
     */
    public function saveCart($sessionId, $vendorIdentifier, array $cart)
    {
        $cartCollection = $this->getCartCollection($sessionId);
        $vendorCartKey = $this->createCartKey($sessionId, $vendorIdentifier);

        foreach ($cartCollection as $key => &$values) {
            $values[static::DEFAULT_CART_FLAG] = false;
        }
        $cartCollection[$vendorCartKey] = [static::DEFAULT_CART_FLAG => true, static::CART_KEY => $cart];

        $this->cache->save($this->createCartCollectionKey($sessionId), $cartCollection);
    }

    /**
     * @param string $sessionId
     * @param string $vendorIdentifier
     */
    public function deleteCart($sessionId, $vendorIdentifier)
    {
        $cartCollection = $this->getCartCollection($sessionId);
        $vendorCartKey = $this->createCartKey($sessionId, $vendorIdentifier);

        if (array_key_exists($vendorCartKey, $cartCollection)) {
            unset($cartCollection[$vendorCartKey]);
        }

        $this->cache->save($this->createCartCollectionKey($sessionId), $cartCollection);
    }

    /**
     * @param string $sessionId
     * @param string $vendorIdentifier
     *
     * @return array|null
     */
    public function getCart($sessionId, $vendorIdentifier)
    {
        $cart = null;
        $cartKey = $this->createCartKey($sessionId, $vendorIdentifier);
        $cartCollection = $this->getCartCollection($sessionId);

        foreach ($cartCollection as $key => $values) {
            if ($key === $cartKey) {
                return $values[static::CART_KEY];
            }
        }

        return $cart;
    }

    /**
     * @param string $sessionId
     * @param string $vendorIdentifier
     *
     * @return array|null
     */
    public function getCartIfDefault($sessionId, $vendorIdentifier)
    {
        $cartKey = $this->createCartKey($sessionId, $vendorIdentifier);
        $cartCollection = $this->getCartCollection($sessionId);

        if (array_key_exists($cartKey, $cartCollection) && $cartCollection[$cartKey][static::DEFAULT_CART_FLAG]) {
            return $cartCollection[$cartKey][static::CART_KEY];
        }

        return null;
    }

    /**
     * @param array $jsonCart
     *
     * @return array
     */
    public function calculateCart(array $jsonCart)
    {
        $response = $this->cartProvider->calculate($jsonCart);

        if (array_key_exists('vendorCart', $response)) {
            $response['orderTime'] = $jsonCart['orderTime'];
        }

        return $this->fixMinDeliveryFeeDiscount($jsonCart['vendor_id'], $response);
    }

    /**
     * @param string $sessionId
     *
     * @return array
     */
    protected function getCartCollection($sessionId)
    {
        $carts = $this->cache->fetch($this->createCartCollectionKey($sessionId));

        return false === $carts ? [] : $carts;
    }

    /**
     * @param string $sessionId
     *
     * @return array
     */
    public function getDefaultCart($sessionId)
    {
        $cartCollection = $this->getCartCollection($sessionId);
        $defaultCart = null;

        foreach ($cartCollection as $values) {
            if ($values[static::DEFAULT_CART_FLAG] || count($defaultCart) === 0) {
                $defaultCart = $values[static::CART_KEY];
            }
        }

        return $defaultCart;
    }

    /**
     * @param string $sessionId
     * @param string $vendorIdentifier
     *
     * @return string
     */
    protected function createCartKey($sessionId, $vendorIdentifier)
    {
        return sprintf('cart:%s_%s', $sessionId, $vendorIdentifier);
    }

    /**
     * @param string $sessionId
     *
     * @return string
     */
    protected function createCartCollectionKey($sessionId)
    {
        return static::CARTS_KEY_PREFIX . $sessionId;
    }

    /**
     * API doesn't fill the delivery_fee_discount attribute correctly
     * Until it's fixed we'll need to fix it here.
     *
     * Assumption :
     *  - we have only one delivery_fee value per vendor
     *
     * @param int $vendorId
     * @param array $apiResult
     *
     * @return array
     */
    protected function fixMinDeliveryFeeDiscount($vendorId, array $apiResult)
    {
        foreach ($apiResult['vendorCart'] as &$vendorCart) {
            $vendor = $this->vendorProvider->find($vendorCart['vendor_id']);
            if ($vendor->getMinimumDeliveryFee() > $vendorCart['delivery_fee']) {
                $vendorCart['delivery_fee_discount'] = $vendor->getMinimumDeliveryFee();
            }
        }

        if ($vendor->getId() !== $vendorId) {
            $vendor = $this->vendorProvider->find($vendorId);
        }

        if ($vendor->getMinimumDeliveryFee() > $apiResult['delivery_fee']) {
            $apiResult['delivery_fee_discount'] = $vendor->getMinimumDeliveryFee();
        }

        return $apiResult;
    }
}
