<?php

namespace Volo\FrontendBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Foodpanda\ApiSdk\Provider\CartProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Foodpanda\ApiSdk\Provider\VendorProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Volo\FrontendBundle\Security\Token;

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
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @param CartProvider $cartProvider
     * @param VendorProvider $vendorProvider
     * @param TokenStorage $tokenStorage
     */
    public function __construct(
        CartProvider $cartProvider,
        VendorProvider $vendorProvider,
        TokenStorage $tokenStorage
    ) {
        $this->cartProvider = $cartProvider;
        $this->vendorProvider = $vendorProvider;
        $this->tokenStorage = $tokenStorage;
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
        $cartOrderTime = $jsonCart['order_time'];
        $this->adjustOrderTime($jsonCart);
        $jsonCart['vouchers'] = $this->prepareVouchersForTheApi($jsonCart['vouchers']);

        /** @var Token $token */
        $token = $this->tokenStorage->getToken();

        $mergedCart = $this->mergeSimilarProducts($jsonCart);
        $accessToken =  $token instanceof Token ? $token->getAccessToken() : null;
        $response = $this->cartProvider->calculate($mergedCart, $accessToken);

        if (count($response['vendorCart']) > 0) {
            $response['vendorCart'][0] = $this->unMergeSimilarProducts($response['vendorCart'][0], $jsonCart);
            $response['vendorCart'][0] = $this->fixFixedVoucherCalculations($response['vendorCart'][0]);
        }

        if (array_key_exists('vendorCart', $response)) {
            $response['order_time'] = $cartOrderTime;

            $response = $this->repopulateSpecialInstructions($jsonCart, $response);
        }

        return $this->fixMinDeliveryFeeDiscount($jsonCart['vendor_id'], $response);
    }

    /**
     * @param array $calculatedCart
     * @param array $jsonCart
     *
     * @return array
     */
    protected function unMergeSimilarProducts(array $calculatedCart, array $jsonCart)
    {
        $unMergedProducts = [];
        foreach ($jsonCart['products'] as $jsonCartProduct) {
            $productKey = $this->createProductIdentificationKey($jsonCartProduct, true);
            foreach ($calculatedCart['products'] as $calculatedCartProduct) {
                if ($productKey === $this->createProductIdentificationKey($calculatedCartProduct, true)) {
                    $multiplier = $jsonCartProduct['quantity'] / $calculatedCartProduct['quantity'];
                    $product = $calculatedCartProduct;
                    $product['total_price_before_discount'] *= $multiplier;
                    $product['total_price'] *= $multiplier;
                    $product['quantity'] = $jsonCartProduct['quantity'];
                    $unMergedProducts[] = $product;
                    break;
                }
            }
        }
        $calculatedCart['products'] = $unMergedProducts;

        return $calculatedCart;
    }

    /**
     * @param array $cart
     *
     * @return array
     */
    public function mergeSimilarProducts(array $cart)
    {
        $mergedCart = $cart;
        $mergedCart['products'] = [];
        foreach ($cart['products'] as $product) {
            $productKey = $this->createProductIdentificationKey($product, true);
            if (array_key_exists($productKey, $mergedCart['products'])) {
                $product['quantity'] += $mergedCart['products'][$productKey]['quantity'];
            }
            $mergedCart['products'][$productKey] = $product;
        }
        $mergedCart['products'] = array_values($mergedCart['products']);

        return $mergedCart;
    }

    /**
     * @param array $cart
     */
    protected function adjustOrderTime(array &$cart)
    {
        if ($cart['order_time'] === OrderManagerService::ORDER_NOW_TIME_PICKER_IDENTIFIER) {
            $cart['order_time'] = date_format(new \DateTime($cart['order_time']), \DateTime::ISO8601);
        } else {
            unset($cart['order_time']);
        }
    }

    /**
     * @param array $cartVouchers
     *
     * @return array
     */
    protected function prepareVouchersForTheApi(array $cartVouchers)
    {
        foreach ($cartVouchers as &$voucher) {
            $voucher = substr($voucher, 0, 16);
        }

        return $cartVouchers;
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
     * @return array|null
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

            if ($vendor->getId() === $vendorId && $vendor->getMinimumDeliveryFee() > $apiResult['delivery_fee']) {
                $apiResult['delivery_fee_discount'] = $vendor->getMinimumDeliveryFee();
            }
        }

        return $apiResult;
    }

    /**
     * @param array $jsonCart
     * @param       $response
     *
     * @return array
     */
    protected function repopulateSpecialInstructions(array $jsonCart, $response)
    {
        $instructions = new ArrayCollection();
        foreach ($jsonCart['products'] as $product) {
            $key = $this->createProductIdentificationKey($product);

            $data = $instructions->containsKey($key) ? $instructions[$key] : [];
            $data[] = $product['special_instructions'];

            $instructions->set($key, $data);
        }

        foreach ($response['vendorCart'] as &$vendorCart) {
            foreach ($vendorCart['products'] as &$responseProduct) {
                $key = $this->createProductIdentificationKey($responseProduct);
                if ($instructions->containsKey($key)) {
                    $data = $instructions[$key];
                    $responseProduct['special_instructions'] = array_shift($data);

                    count($data) === 0 ? $instructions->remove($key) : $instructions->set($key, $data);
                }
            }
        }

        return $response;
    }

    /**
     * @param array $product
     * @param bool $ignoreQuantity
     *
     * @return string
     */
    protected function createProductIdentificationKey(array $product, $ignoreQuantity = false)
    {
        return sprintf(
            '%s_%d_%s',
            array_key_exists('variation_id', $product) ? $product['variation_id'] : $product['product_variation_id'],
            $ignoreQuantity ? 1 : $product['quantity'],
            implode('_', array_column($product['toppings'], 'id'))
        );
    }

    /**
     * @param array $vendorCart
     *
     * @return array
     */
    protected function fixFixedVoucherCalculations($vendorCart)
    {
        foreach (['total_value', 'group_joiner_total'] as $key) {
            $vendorCart[$key] = max($vendorCart[$key], .0);
        }

        return $vendorCart;
    }
}
