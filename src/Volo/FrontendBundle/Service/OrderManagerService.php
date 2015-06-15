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
     *
     * @param GuestCustomer $guestCustomer
     * @param float         $expectedAmount
     * @param int           $paymentTypeId
     * @param array         $cart
     *
     * @return array
     */
    public function placeGuestOrder(GuestCustomer $guestCustomer, $expectedAmount, $paymentTypeId, array $cart)
    {
        $order = [
            'location'                             => $cart['location'],
            'products'                             => $cart['products'],
            'vouchers'                             => $cart['vouchers'],
            'expedition_type'                      => 'delivery',
            'order_time'                           => date_format(new \DateTime($cart['order_time']) ,\DateTime::ISO8601),
            'payment_type_id'                      => $paymentTypeId,
            'customer_address_id'                  => $guestCustomer->getCustomerAddress()->getId(),
            'customer_id'                          => $guestCustomer->getCustomer()->getId(),
            'customer_mail'                        => $guestCustomer->getCustomer()->getEmail(),
            'customer_comment'                     => '',
            'expected_total_amount'                => $expectedAmount,
            'source'                               => $this->apiClientId,
            'trigger_hosted_payment_page_handling' => true,
        ];

        return $this->orderProvider->guestOrder($order);
    }

    /**
     * TODO: payment_type_id is hardcoded
     * TODO: Order comment isn't handled
     *
     * @param AccessToken $accessToken
     * @param int         $addressId
     * @param float       $expectedAmount
     * @param int         $paymentTypeId
     * @param array       $cart
     *
     * @return array
     */
    public function placeOrder(AccessToken $accessToken, $addressId, $expectedAmount, $paymentTypeId, array $cart)
    {
        $order = [
            'location'                             => $cart['location'],
            'products'                             => $cart['products'],
            'vouchers'                             => $cart['vouchers'],
            'order_time'                           => date_format(new \DateTime($cart['order_time']) ,\DateTime::ISO8601),
            'expedition_type'                      => 'delivery',
            'payment_type_id'                      => $paymentTypeId,
            'customer_address_id'                  => $addressId,
            'customer_comment'                     => '',
            'expected_total_amount'                => $expectedAmount,
            'source'                               => $this->apiClientId,
            'trigger_hosted_payment_page_handling' => true,
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
     * @param AccessToken $accessToken
     * @param array       $order
     *
     * @return array
     */
    public function payment(AccessToken $accessToken, array $order)
    {
        $paymentRequest = [
            'order_code'             => $order['code'],
            'amount'                 => $order['total_value'],
        ];

        switch (true) {
            case array_key_exists('credit_card_id', $order):
                $paymentRequest['credit_card_id'] = $order['credit_card_id'];
                break;

            case array_key_exists('encrypted_payment_data', $order):
                $paymentRequest['encrypted_payment_data']      = $order['encrypted_payment_data'];
                $paymentRequest['is_credit_card_store_active'] = 'true';
                break;

            default:
                throw new \RuntimeException('No recurring or CSE payment information provided');
        }

        return $this->orderProvider->payment($accessToken, $paymentRequest);
    }
}
