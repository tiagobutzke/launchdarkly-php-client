<?php

namespace Volo\FrontendBundle\Controller;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\Order\GuestCustomer;
use Foodpanda\ApiSdk\Entity\PaymentType\PaymentType;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Foodpanda\ApiSdk\Exception\ValidationEntityException;
use Foodpanda\ApiSdk\Provider\CustomerProvider;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Volo\FrontendBundle\Exception\Payment\PaymentMethodException;
use Volo\FrontendBundle\Service\CustomerLocationService;

/**
 * @Route("/checkout")
 */
class CheckoutController extends BaseController
{
    /**
     * @Route("/{vendorCode}/payment", name="checkout_payment", options={"expose"=true})
     * @Method({"GET"})
     * @Template()
     *
     * @param Request $request
     * @param string $vendorCode
     *
     * @return array
     */
    public function paymentAction(Request $request, $vendorCode)
    {
        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($vendorCode);
        } catch (ApiErrorException $exception) {
            throw new NotFoundHttpException('Vendor invalid', $exception);
        }
        $session = $request->getSession();
        $usedVoucher = '';
        $paymentErrorMessage = null;
        try {
            $cart = $this->getCart($session, $vendor);
        } catch (\RuntimeException $exception) {
            return $this->redirectToRoute('vendor', ['code' => $vendor->getCode(), 'urlKey' => $vendor->getUrlKey()]);
        }

        $configuration = $this->get('volo_frontend.service.configuration')->getConfiguration();
        $customerLocationService = $this->get('volo_frontend.service.customer_location');
        $location = $customerLocationService->get($session);
        $formattedLocation = $customerLocationService->format($location);
        $serializer = $this->get('volo_frontend.api.serializer');

        $restaurantLocation = [
            'city' => $vendor->getCity()->getName(),
            'postcode' => $location[CustomerLocationService::KEY_PLZ],
            'street' => $location[CustomerLocationService::KEY_STREET],
            'building' => $location[CustomerLocationService::KEY_BUILDING],
        ];

        try {
            $calculatedCart = $this->get('volo_frontend.service.cart_manager')->calculateCart($cart);
        } catch (ApiErrorException $e) {
            $calculatedCart = null;

            $error = json_decode($e->getJsonErrorMessage(), true);
            if (isset($error['data']['exception_type'])) {
                $paymentErrorMessage = $this->get('translator')->trans($error['data']['exception_type']);
            };

            // Show invalid voucher to the user
            if (isset($cart['vouchers'][0])) {
                $usedVoucher = $cart['vouchers'][0];
            }
        }

        // <INTVOLO-472>
        // https://jira.rocket-internet.de/browse/INTVOLO-472
        // Temporary fixing the state of system to find the root cause of the issue
        $this->locationMonitoringSaveState(
            'paymentAction',
            $location,
            [
                'vendor_code' => $vendor->getCode(),
                $calculatedCart
            ]
        );
        // </INTVOLO-472>

        if ($this->container->getParameter('country_code') === 'fi') {
            $invoice = $this->getSerializer()->denormalize(['id' => 6, 'payment_type_code' => 'invoice'], PaymentType::class);
            $vendor->getPaymentTypes()->add($invoice);
        }

        $viewData = [
            'cart'               => $calculatedCart,
            'vendor'             => $vendor,
            'adyen_public_key'   => $configuration->getAdyenEncryptionPublicKey(),
            'address'            => is_array($location) ? $location[CustomerLocationService::KEY_ADDRESS] : '',
            'location'           => $location,
            'formattedLocation'  => $formattedLocation,
            'isDeliverable'      => is_array($location),
            'default_address'    => $restaurantLocation,
            'customer'           => $serializer->normalize(new Customer()),
            'customer_addresses' => [],
            'usedVoucher'        => $usedVoucher,
            'paymentErrorMessage' => $paymentErrorMessage,
            'showSpecialInstructionsTutorial' => false,
        ];

        $viewData = $this->addViewDataForAuthenticatedUser($vendor, $viewData);

        return $viewData;
    }

    /**
     * @param Vendor $vendor
     * @param array $viewData
     *
     * @return array
     */
    private function addViewDataForAuthenticatedUser(Vendor $vendor, array $viewData)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $token = $this->getToken();
            $serializer = $this->get('volo_frontend.api.serializer');

            $customerAddresses = $this->getCustomerService()->getAddresses($token->getAccessToken(), $vendor->getId());

            $viewData['customer_addresses'] = $serializer->normalize($customerAddresses);
            $viewData['customer'] = $serializer->normalize($token->getAttributes()['customer']);
            $viewData['customer_cards']
                = $this->getCustomerProvider()->getAdyenCards($token->getAccessToken())['items'];
        }

        return $viewData;
    }

    /**
     * @Route(
     *      "/{vendorCode}/pay",
     *      name="checkout_place_order",
     *      defaults={"_format": "json"},
     *      options={"expose"=true},
     *      condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     *
     * @param Request $request
     * @param string $vendorCode
     *
     * @return JsonResponse
     */
    public function placeOrderAction(Request $request, $vendorCode)
    {
        try {
            $data = $this->decodeJsonContent($request->getContent());
        } catch (BadRequestHttpException $e) {
            $text = $this->get('translator')->trans('json_error.invalid_request');

            return new JsonResponse(
                ['data' => ['error' => ['errors' => ['developer_message' => $text]]]],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($vendorCode);
        } catch (ApiErrorException $e) {
            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        }
        $cart = $this->getCart($request->getSession(), $vendor);

        // <INTVOLO-472>
        // https://jira.rocket-internet.de/browse/INTVOLO-472
        // Temporary fixing the state of system to find the root cause of the issue
        $this->locationMonitoringSaveState(
            'paymentAction',
            $cart['location'],
            [
                'vendor_code' => $vendor->getCode(),
                $cart
            ]
        );
        // </INTVOLO-472>

        $data['client_ip'] = $request->getClientIp();

        $guestCustomer = null;
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            try {
                $guestCustomer = $this->getCustomerService()->createGuestCustomer(
                    $data['customer'],
                    $data['address']
                );
            } catch (ValidationEntityException $e) {
                return new JsonResponse(['errors' => $e->getValidationMessages()], Response::HTTP_BAD_REQUEST);
            } catch (ApiErrorException $e) {
                $error = json_decode($e->getJsonErrorMessage(), true);
                if (isset($error['data']['exception_type']) &&
                    'ApiCustomerAlreadyExistsException' === $error['data']['exception_type']
                ) {
                    return new JsonResponse(['exists' => true], Response::HTTP_BAD_REQUEST);
                }
            }

            $request->getSession()->set(
                OrderController::SESSION_GUEST_ORDER_ACCESS_TOKEN,
                $guestCustomer->getAccessToken()
            );

            $customer = $guestCustomer->getCustomer();
            $accessToken = new AccessToken($guestCustomer->getAccessToken(), 'bearer');
        } else {
            $accessToken = $this->getToken()->getAccessToken();
            $customer = $this->getCustomerService()->getCustomer($accessToken);
        }

        try {
            $apiResult = $this->handleOrder($request->getSession(), $cart, $data, $guestCustomer);
        } catch (PaymentMethodException $e) {
            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        } catch (ApiErrorException $e) {
            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        }

        if (isset($data['isSubscribedNewsletter'])) {
            $this->updateSubscriptionNewsletter(
                (bool)$data['isSubscribedNewsletter'],
                $customer,
                $vendor,
                $accessToken
            );
        }

        // if we're using hosted payment, at this point the order is placed but not paid.
        if ($apiResult['hosted_payment_page_redirect'] === null) {
            $this->get('volo_frontend.service.cart_manager')->deleteCart($request->getSession(), $vendor->getId());
        }

        $response = new JsonResponse($apiResult);
        $response->headers->setCookie(new Cookie('orderPay', 'true', 0, '/', null, $request->isSecure(), false));

        return $response;
    }

    /**
     * @param SessionInterface $session
     * @param Vendor $vendor
     *
     * @throws \RuntimeException
     * @return array
     */
    protected function getCart(SessionInterface $session, Vendor $vendor)
    {
        $cart = $this->get('volo_frontend.service.cart_manager')->getCart($session, $vendor->getId());

        if ($cart === null || count($cart['products']) === 0) {
            throw new \RuntimeException(sprintf('No cart found for vendor %s', $vendor->getCode()));
        }

        return $cart;
    }

    /**
     * @param SessionInterface $session
     * @param array $cart
     * @param array $data
     * @param GuestCustomer $guestCustomer
     *
     * @return array
     */
    protected function handleOrder(
        SessionInterface $session,
        array $cart,
        array $data,
        GuestCustomer $guestCustomer = null
    ) {
        $orderManager = $this->get('volo_frontend.service.order_manager');

        $expectedAmount = $data['expected_total_amount'];
        $paymentTypeId = $data['payment_type_id'];
        $session->set(OrderController::SESSION_ORDER_PAYMENT_CODE, $data['payment_type_code']);
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $token = $this->getToken();
            $addressId = $data['customer_address_id'];

            $order = $orderManager->placeOrder(
                $token->getAccessToken(),
                $addressId,
                $expectedAmount,
                $paymentTypeId,
                $cart
            );
            if ($order['hosted_payment_page_redirect'] === null && !in_array($data['payment_type_code'], ['cod', 'invoice'])) {
                $orderManager->payment($token->getAccessToken(), $data + $order);
            }
        } else {
            $order = $orderManager->placeGuestOrder(
                $guestCustomer,
                $expectedAmount,
                $paymentTypeId,
                $cart
            );
            if ($order['hosted_payment_page_redirect'] === null && !in_array($data['payment_type_code'], ['cod', 'invoice'])) {
                $orderManager->guestPayment($order, $data['encrypted_payment_data'], $data['client_ip']);
            }
        }

        // <INTVOLO-472>
        // https://jira.rocket-internet.de/browse/INTVOLO-472
        // Temporary fixing the state of system to find the root cause of the issue
        $this->locationMonitoringSaveState(
            'handleOrder',
            $cart['location'],
            array_diff_key($data, ['encrypted_payment_data' => ''])
        );
        // </INTVOLO-472>

        return $order;
    }

    /**
     * @return CustomerProvider
     */
    private function getCustomerProvider()
    {
        return $this->get('volo_frontend.provider.customer');
    }

    /**
     * @param bool $isSubscribedNewsletter
     * @param Customer $customer
     * @param Vendor $vendor
     * @param AccessToken $accessToken
     */
    private function updateSubscriptionNewsletter(
        $isSubscribedNewsletter,
        Customer $customer,
        Vendor $vendor,
        AccessToken $accessToken
    ) {
        try {
            if ($isSubscribedNewsletter) {
                $this->get('volo_frontend.provider.newsletter')->subscribe(
                    $customer->getEmail(),
                    $vendor->getCity()->getId(),
                    $accessToken
                );
            } else {
                $this->get('volo_frontend.provider.newsletter')->unsubscribe($accessToken);
            }
        } catch (ApiErrorException $exception) {
            // these endpoints are throwing exceptions if you try to unsubscribe while not being subscribed
            // or the other way around, we simply ignore that.
        }
    }

    /**
     * @param string $logName
     * @param array $location
     * @param array $data
     */
    private function locationMonitoringSaveState($logName, array $location, array $data)
    {
        try {
            $lat = (float) $location[CustomerLocationService::KEY_LAT];
            $lng = (float) $location[CustomerLocationService::KEY_LNG];

            if ($lat === .0 || $lng === .0) {
                $this->getLogger()->error(
                    "location_monitoring_save_state::$logName",
                    [
                        'data' => $data,
                        'location' => $location
                    ]
                );
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger()
    {
        return $this->get('logger');
    }
}
