<?php

namespace Volo\FrontendBundle\Service;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Provider\CartProvider;
use Foodpanda\ApiSdk\Provider\OrderProvider;
use Foodpanda\ApiSdk\Provider\CustomerProvider;
use Foodpanda\ApiSdk\Entity\Order\GuestCustomer;

class OrderManagerService
{
    /**
     * @var OrderProvider
     */
    protected $orderProvider;

    /**
     * @var CustomerProvider
     */
    protected $customerProvider;

    /**
     * @var CartProvider
     */
    protected $cartProvider;

    /**
     * @var string
     */
    protected $apiClientId;

    /**
     * @param OrderProvider    $orderProvider
     * @param CustomerProvider $customerProvider
     * @param CartProvider     $cartProvider
     * @param string           $apiClientId
     */
    public function __construct(
        OrderProvider $orderProvider,
        CustomerProvider $customerProvider,
        CartProvider $cartProvider,
        $apiClientId
    ) {
        $this->orderProvider    = $orderProvider;
        $this->customerProvider = $customerProvider;
        $this->cartProvider     = $cartProvider;
        $this->apiClientId      = $apiClientId;
    }

    /**
     * TODO: payment_type_id is hardcoded
     * TODO: Order comment isn't handled
     * TODO: Handle vendor closed
     *
     * @param GuestCustomer $guestCustomer
     * @param array         $cart
     *
     * @return array
     */
    public function placeGuestOrder(GuestCustomer $guestCustomer, array $cart)
    {
        $recalculatedCart = $this->cartProvider->calculate($cart);

        $order = [
            'location'              => $cart['location'],
            'products'              => $cart['products'],
            'expedition_type'       => 'delivery',
            'payment_type_id'       => 5,
            'customer_address_id'   => $guestCustomer->getCustomerAddress()->getId(),
            'customer_id'           => $guestCustomer->getCustomer()->getId(),
            'customer_mail'         => $guestCustomer->getCustomer()->getEmail(),
            'customer_comment'      => '',
            'expected_total_amount' => $recalculatedCart['total_value'],
            'source'                => $this->apiClientId,
        ];

        return $this->orderProvider->guestOrder($order);
    }

    /**
     * TODO: payment_type_id is hardcoded
     * TODO: Order comment isn't handled
     * TODO: Handle vendor closed
     *
     * @param AccessToken $accessToken
     * @param int         $addressId
     * @param array       $cart
     *
     * @return array
     */
    public function placeOrder(AccessToken $accessToken, $addressId, array $cart)
    {
        $recalculatedCart = $this->cartProvider->calculate($cart);

        $order = [
            'location'              => $cart['location'],
            'products'              => $cart['products'],
            'expedition_type'       => 'delivery',
            'payment_type_id'       => 5,
            'customer_address_id'   => $addressId,
            'customer_comment'      => '',
            'expected_total_amount' => $recalculatedCart['total_value'],
            'source'                => $this->apiClientId,
        ];

        return $this->orderProvider->order($accessToken, $order);
    }

    /**
     * @param string $adyenEncryptedData
     * @param array  $order
     *
     * @return array
     */
    public function guestPayment(array $order, $adyenEncryptedData)
    {
        $paymentRequest = [
            'order_code'             => $order['code'],
            'customer_id'            => $order['customer']['id'],
            'customer_email'         => $order['customer']['email'],
            'amount'                 => $order['total_value'],
            'encrypted_payment_data' => $adyenEncryptedData,
        ];

        return $this->orderProvider->guestPayment($paymentRequest);
    }
    
    /**
     * @param string $adyenEncryptedData
     * @param array  $order
     *
     * @return array
     */
    public function payment(AccessToken $accessToken, array $order, $adyenEncryptedData)
    {
        $paymentRequest = [
            'order_code'             => $order['code'],
            'amount'                 => $order['total_value'],
            'encrypted_payment_data' => $adyenEncryptedData,
        ];

        return $this->orderProvider->payment($accessToken, $paymentRequest);
    }
}
