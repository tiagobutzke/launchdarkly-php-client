<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Provider\CartProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Foodpanda\ApiSdk\Provider\VendorProvider;

class CartManagerService
{
    const CARTS_KEY_PREFIX = 'customer:carts';
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
     * @param CartProvider   $cartProvider
     * @param VendorProvider $vendorProvider
     */
    public function __construct(
        CartProvider $cartProvider,
        VendorProvider $vendorProvider
    ) {
        $this->cartProvider = $cartProvider;
        $this->vendorProvider = $vendorProvider;
    }

    /**
     * @param SessionInterface $session
     * @param string $vendorIdentifier
     * @param array $cart
     */
    public function saveCart($session, $vendorIdentifier, array $cart)
    {
        $cartCollection = $this->getCartCollection($session);
        $vendorCartKey = $this->createCartKey($vendorIdentifier);

        foreach ($cartCollection as $key => &$values) {
            $values[static::DEFAULT_CART_FLAG] = false;
        }
        $cartCollection[$vendorCartKey] = [static::DEFAULT_CART_FLAG => true, static::CART_KEY => $cart];

        $session->set($this->createCartCollectionKey(), $cartCollection);
    }

    /**
     * @param SessionInterface $session
     * @param string $vendorIdentifier
     */
    public function deleteCart($session, $vendorIdentifier)
    {
        $cartCollection = $this->getCartCollection($session);
        $vendorCartKey = $this->createCartKey($vendorIdentifier);

        if (array_key_exists($vendorCartKey, $cartCollection)) {
            unset($cartCollection[$vendorCartKey]);
        }

        $session->set($this->createCartCollectionKey(), $cartCollection);
    }

    /**
     * @param SessionInterface $session
     * @param string $vendorIdentifier
     *
     * @return array|null
     */
    public function getCart($session, $vendorIdentifier)
    {
        $cart = null;
        $cartKey = $this->createCartKey($vendorIdentifier);
        $cartCollection = $this->getCartCollection($session);

        if (isset($cartCollection[$cartKey])) {
            $cart = $cartCollection[$cartKey][static::CART_KEY];
        }

        return $cart;
    }

    /**
     * @param SessionInterface $session
     * @param string $vendorIdentifier
     *
     * @return array|null
     */
    public function getCartIfDefault($session, $vendorIdentifier)
    {
        $cartKey = $this->createCartKey($vendorIdentifier);
        $cartCollection = $this->getCartCollection($session);

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
        $jsonCart['order_time'] = date_format(new \DateTime($jsonCart['order_time']), \DateTime::ISO8601);

        $response = $this->cartProvider->calculate($jsonCart);

        if (array_key_exists('vendorCart', $response)) {
            $response['order_time'] = $jsonCart['order_time'];
        }

        return $this->fixMinDeliveryFeeDiscount($jsonCart['vendor_id'], $response);
    }

    /**
     * @param SessionInterface $session
     *
     * @return array
     */
    protected function getCartCollection($session)
    {
        return $session->get($this->createCartCollectionKey(), []);
    }

    /**
     * @param SessionInterface $session
     *
     * @return array
     */
    public function getDefaultCart($session)
    {
        $cartCollection = $this->getCartCollection($session);
        $defaultCart = null;

        foreach ($cartCollection as $values) {
            if ($values[static::DEFAULT_CART_FLAG] || count($defaultCart) === 0) {
                $defaultCart = $values[static::CART_KEY];
            }
        }

        return $defaultCart;
    }

    /**
     * @param string $vendorIdentifier
     *
     * @return string
     */
    protected function createCartKey($vendorIdentifier)
    {
        return sprintf('%s:%s', static::CART_KEY, $vendorIdentifier);
    }

    /**
     * @return string
     */
    protected function createCartCollectionKey()
    {
        return static::CARTS_KEY_PREFIX;
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
