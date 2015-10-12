<?php

namespace Volo\FrontendBundle\Service;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Provider\OrderProvider;
use Foodpanda\ApiSdk\Provider\CustomerProvider;
use Foodpanda\ApiSdk\Entity\Order\GuestCustomer;

class OrderManagerService
{
    const ORDER_NOW_TIME_PICKER_IDENTIFIER = 'now';
    /**
     * @var OrderProvider
     */
    protected $orderProvider;

    /**
     * @var CustomerProvider
     */
    protected $customerProvider;

    /**
     * @var CartManagerService
     */
    protected $cartManager;

    /**
     * @var string
     */
    protected $apiClientId;

    /**
     * @var VendorService
     */
    protected $vendorService;

    /**
     * @param OrderProvider      $orderProvider
     * @param CustomerProvider   $customerProvider
     * @param CartManagerService $cartManagerService
     * @param VendorService      $vendorService
     * @param string             $apiClientId
     */
    public function __construct(
        OrderProvider $orderProvider,
        CustomerProvider $customerProvider,
        CartManagerService $cartManagerService,
        VendorService $vendorService,
        $apiClientId
    ) {
        $this->orderProvider    = $orderProvider;
        $this->customerProvider = $customerProvider;
        $this->cartManager      = $cartManagerService;
        $this->apiClientId      = $apiClientId;
        $this->vendorService    = $vendorService;
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
        // First get comment then merge, when we merge the products we loose the comments.
        $customerComment = $this->buildCustomerComment($cart);
        $cart = $this->cartManager->mergeSimilarProducts($cart);

        $order = [
            'location'                             => $cart['location'],
            'products'                             => $cart['products'],
            'vouchers'                             => $this->prepareVouchersForTheApi($cart['vouchers']),
            'expedition_type'                      => 'delivery',
            'payment_type_id'                      => $paymentTypeId,
            'customer_address_id'                  => $guestCustomer->getCustomerAddress()->getId(),
            'customer_id'                          => $guestCustomer->getCustomer()->getId(),
            'customer_mail'                        => $guestCustomer->getCustomer()->getEmail(),
            'customer_comment'                     => $customerComment,
            'expected_total_amount'                => $expectedAmount,
            'source'                               => $this->apiClientId,
            'trigger_hosted_payment_page_handling' => true,
        ];
        $this->addOrderTimeIfApplicable($order, $cart);

        return $this->orderProvider->guestOrder($order);
    }

    /**
     * @param array $order
     * @param array $cart
     */
    protected function addOrderTimeIfApplicable(array &$order, array $cart)
    {
        if ($cart['order_time'] !== static::ORDER_NOW_TIME_PICKER_IDENTIFIER) {
            $order['order_time'] = date_format(new \DateTime($cart['order_time']), \DateTime::ISO8601);
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
        // First get comment then merge, when we merge the products we loose the comments.
        $customerComment = $this->buildCustomerComment($cart);
        $cart = $this->cartManager->mergeSimilarProducts($cart);

        $order = [
            'location'                             => $cart['location'],
            'products'                             => $cart['products'],
            'vouchers'                             => $this->prepareVouchersForTheApi($cart['vouchers']),
            'expedition_type'                      => 'delivery',
            'payment_type_id'                      => $paymentTypeId,
            'customer_address_id'                  => $addressId,
            'customer_comment'                     => $customerComment,
            'expected_total_amount'                => $expectedAmount,
            'source'                               => $this->apiClientId,
            'trigger_hosted_payment_page_handling' => true,
        ];
        $this->addOrderTimeIfApplicable($order, $cart);

        return $this->orderProvider->order($accessToken, $order);
    }

    /**
     * @param string $adyenEncryptedData
     * @param array  $order
     * @param string $clientIp
     *
     * @return array
     */
    public function guestPayment(array $order, $adyenEncryptedData, $clientIp)
    {
        $paymentRequest = [
            'order_code'             => $order['code'],
            'customer_id'            => $order['customer']['id'],
            'customer_email'         => $order['customer']['email'],
            'amount'                 => $order['total_value'],
            'encrypted_payment_data' => $adyenEncryptedData,
            'client_ip'              => $clientIp,
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
            'client_ip'              => $order['client_ip'],
        ];

        switch (true) {
            case array_key_exists('credit_card_id', $order):
                $paymentRequest['credit_card_id'] = $order['credit_card_id'];
                break;

            case array_key_exists('encrypted_payment_data', $order):
                $paymentRequest['encrypted_payment_data']      = $order['encrypted_payment_data'];
                $paymentRequest['is_credit_card_store_active'] = $order['is_credit_card_store_active'];
                break;

            default:
                throw new \RuntimeException('No recurring or CSE payment information provided');
        }

        return $this->orderProvider->payment($accessToken, $paymentRequest);
    }

    /**
     * @param array $cart
     *
     * @return string
     */
    protected function buildCustomerComment(array $cart)
    {
        $customerComment = '';

        foreach ($cart['products'] as $cartProduct) {
            if ($cartProduct['special_instructions'] === '') {
                continue;
            }

            try {
                $product = $this->vendorService->getProduct($cartProduct['vendor_id'], $cartProduct['variation_id']);
            } catch (\RuntimeException $exception) {
                continue;
            }

            $customerComment .= sprintf(
                'Comment for %s %s, %s: "%s"%s',
                $cartProduct['quantity'],
                $product->getName(),
                implode(', ', array_column($cartProduct['toppings'], 'name')),
                $cartProduct['special_instructions'],
                PHP_EOL
            );
        }

        return $customerComment;
    }
}
