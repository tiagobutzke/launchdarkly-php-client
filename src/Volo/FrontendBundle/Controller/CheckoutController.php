<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Address\Address;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Foodpanda\ApiSdk\Exception\ApiException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Volo\FrontendBundle\Service\CustomerLocationService;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;

/**
 * @Route("/checkout")
 */
class CheckoutController extends Controller
{
    const SESSION_DELIVERY_KEY_TEMPLATE = 'checkout-%s-delivery';
    const SESSION_CONTACT_KEY_TEMPLATE  = 'checkout-%s-contact';
    const SESSION_GUEST_CUSTOMER_KEY_TEMPLATE = 'checkout-guest';

    /**
     * @Route("/{vendorCode}/delivery", name="checkout_delivery_information")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Request $request
     * @param string  $vendorCode
     *
     * @return array
     */
    public function deliveryInformationAction(Request $request, $vendorCode)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('checkout_payment', ['vendorCode' => $vendorCode]);
        }

        $sessionDeliveryKey = sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode);

        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($vendorCode);
        } catch (ApiErrorException $exception) {
            throw new NotFoundHttpException('Vendor invalid', $exception);
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            $address = $this->sanitizeInputData($request->request->get('customer_address', []));

            $request->getSession()->set($sessionDeliveryKey, $address);
            
            return $this->redirect($this->generateUrl('checkout_contact_information', ['vendorCode' => $vendorCode]));
        }

        $address = $request->getSession()->get($sessionDeliveryKey, []);
        $customerLocationService = $this->get('volo_frontend.service.customer_location');
        $defaultAddress = [
            'postcode' => $customerLocationService->get($request->getSession())[CustomerLocationService::KEY_PLZ],
            'city' => $vendor->getCity()->getName()
        ];
        $cartManager = $this->get('volo_frontend.service.cart_manager');
        $location = $this->get('volo_frontend.service.customer_location')->get($request->getSession());

        return [
            'cart'             => $cartManager->calculateCart($this->getCart($vendor)),
            'customer_address' => $defaultAddress + $address,
            'vendor'           => $vendor,
            'address'          => is_array($location) ? $location[CustomerLocationService::KEY_ADDRESS] : '',
            'location'         => $location,
            'isDeliverable'    => is_array($location),
        ];
    }

    /**
     * @Route("/{vendorCode}/contact", name="checkout_contact_information")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Request $request
     * @param string  $vendorCode
     *
     * @return array
     */
    public function contactInformationAction(Request $request, $vendorCode)
    {
        $errorMessages = [];
        $session = $this->get('session');

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('checkout_payment', ['vendorCode' => $vendorCode]);
        }

        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($vendorCode);
        } catch (ApiErrorException $exception) {
            throw new NotFoundHttpException('Vendor invalid', $exception);
        }

        if (!$session->has(sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode))) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'No contact information found');
        }
        $username = '';
        $phoneNumberError = '';
        // We set it to the session data to handle the "Edit" case
        $customerData = $session->get(sprintf(static::SESSION_CONTACT_KEY_TEMPLATE, $vendorCode));
        if ($request->getMethod() === Request::METHOD_POST) {
            // Here override it to handle the form/post with validation case
            $customerData = $this->sanitizeInputData($request->request->get('customer', []));
            $username = $customerData['email'];
            try {
                $phoneNumberService = $this->get('volo_frontend.service.phone_number');
                $parsedNumber = $phoneNumberService->parsePhoneNumber($customerData['mobile_number']);
                $phoneNumberService->validateNumber($parsedNumber);

                $customerData['mobile_number'] = $parsedNumber->getNationalNumber();
                $customerData['mobile_country_code'] = '+' . $parsedNumber->getCountryCode();

                if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                    $guestCustomer = $this->get('volo_frontend.service.customer')->createGuestCustomer(
                        $customerData,
                        $session->get(sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendor->getCode()))
                    );

                    try {
                        if (array_key_exists('newsletter_checkbox', $customerData)) {
                            $this->get('volo_frontend.provider.newsletter')->subscribe(
                                $username,
                                $vendor->getCity()->getId()
                            );
                        }
                    } catch (ApiErrorException $exception) {
                        // this endpoint is throwing an exception if you try to subscribe while being subscribed
                    }

                    $session->set(OrderController::SESSION_GUEST_ORDER_ACCESS_TOKEN, $guestCustomer->getAccessToken());
                    $session->set(static::SESSION_GUEST_CUSTOMER_KEY_TEMPLATE, $guestCustomer);
                }
            } catch (PhoneNumberValidationException $e) {
                $phoneNumberError = $e->getMessage();
            } catch (ApiException $e) {
                $errorMessages[] = $e->getMessage();
            }

            if ('' === $phoneNumberError && count($errorMessages) === 0) {
                $session->set(
                    sprintf(static::SESSION_CONTACT_KEY_TEMPLATE, $vendorCode),
                    $customerData
                );

                return $this->redirect($this->generateUrl('checkout_payment', ['vendorCode' => $vendorCode]));
            }
        }

        $cart = $this->getCart($vendor);
        $location = $this->get('volo_frontend.service.customer_location')->get($session);

        return [
            'phoneNumberError' => $phoneNumberError,
            'username'         => $username,
            'errorMessages'    => $errorMessages,
            'cart'             => $this->get('volo_frontend.service.cart_manager')->calculateCart($cart),
            'vendor'           => $vendor,
            'customer_address' => $session->get(sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode)),
            'customer'         => $customerData,
            'address'          => is_array($location) ? $location[CustomerLocationService::KEY_ADDRESS] : '',
            'location'         => $location,
            'isDeliverable'    => is_array($location),
        ];
    }

    /**
     * @Route("/{vendorCode}/payment", name="checkout_payment")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Request $request
     * @param string  $vendorCode
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
        $cart    = $this->getCart($vendor);
        $session = $this->get('session');

        $sessionDeliveryKey = sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode);
        $sessionContactKey  = sprintf(static::SESSION_CONTACT_KEY_TEMPLATE, $vendorCode);

        if ((!$session->has($sessionDeliveryKey) || !$session->has($sessionContactKey))
            && !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
        ) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'No contact information found');
        }

        $configuration = $this->get('volo_frontend.service.configuration')->getConfiguration();
        $location = $this->get('volo_frontend.service.customer_location')->get($request->getSession());

        $viewData = [
            'cart'             => $this->get('volo_frontend.service.cart_manager')->calculateCart($cart),
            'vendor'           => $vendor,
            'adyen_public_key' => $configuration->getAdyenEncryptionPublicKey(),
            'address'          => is_array($location) ? $location[CustomerLocationService::KEY_ADDRESS] : '',
            'location'         => $location,

            'isDeliverable'    => is_array($location),
        ];

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var \Volo\FrontendBundle\Security\Token $token */
            $token            = $this->get('security.token_storage')->getToken();
            $serializer       = $this->get('volo_frontend.api.serializer');
            $customerProvider = $this->get('volo_frontend.provider.customer');

            $addresses = $customerProvider->getAddresses($token->getAccessToken());

            $customerLocationService = $this->get('volo_frontend.service.customer_location');
            $viewData['default_address'] = [
                'postcode' => $customerLocationService->get($session)[CustomerLocationService::KEY_PLZ],
                'city' => $vendor->getCity()->getName()
            ];
            $viewData['customer_addresses'] = $serializer->normalize($addresses)['items'];
            $viewData['customer']           = $serializer->normalize($token->getAttributes()['customer']);
            $viewData['customer_cards']     = $customerProvider->getAdyenCards($token->getAccessToken())['items'];
        } else {
            $viewData['customer_address'] = $session->get($sessionDeliveryKey);
            $viewData['customer']         = $session->get($sessionContactKey);
        }

        return $viewData;
    }

    /**
     * @Route("/{vendorCode}/pay", name="checkout_place_order", options={"expose"=true})
     * @Method({"POST"})
     *
     * @param Request $request
     * @param string  $vendorCode
     *
     * @return JsonResponse
     */
    public function placeOrderAction(Request $request, $vendorCode)
    {
        $content = $request->getContent();

        if ('' === $content) {
            $text = $this->get('translator')->trans('json_error.invalid_request');
            return new JsonResponse(
                ['data' => ['error' => ['errors' => ['developer_message' => $text]]]],
                Response::HTTP_BAD_REQUEST
            );
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
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
        $cart = $this->getCart($vendor);

        $data['client_ip'] = $request->getClientIp();

        try {
            $apiResult = $this->handleOrder($cart, $vendor, $data);
        } catch (ApiErrorException $e) {
            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        }

        $response = new JsonResponse($apiResult);
        $response->headers->setCookie(new Cookie('orderPay', 'true', 0, '/', null, $request->isSecure(), false));

        return $response;
    }

    /**
     * @Route("/checkout/create_address", name="checkout_create_address", options={"expose"=true})
     * @Method({"POST"})
     * @Template("VoloFrontendBundle:Checkout/Partial:delivery_information_list.html.twig")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAddressAction(Request $request)
    {
        $serializer  = $this->get('volo_frontend.api.serializer');
        $accessToken = $this->get('security.token_storage')->getToken()->getAccessToken();

        $data = $this->sanitizeInputData($request->request->get('customer_address', []));
        $address = $serializer->denormalize($data, Address::class);

        $this->get('volo_frontend.provider.customer')->createAddress($accessToken, $address);
        $addresses = $this->get('volo_frontend.provider.customer')->getAddresses($accessToken);

        return new JsonResponse($serializer->normalize($addresses)['items']);
    }

    /**
     * @deprecated Deprecated route, please remove it in the next release
     *
     * @Route("/edit_contact_information", name="checkout_edit_contact_information_deprecated")
     * @Method({"POST"})
     * @Template("VoloFrontendBundle:Checkout/Partial:contact_information_edit.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function editContactInformationDeprecatedAction(Request $request)
    {
        try {
            $customerParameters = $this->sanitizeInputData($request->request->get('customer', []));
            $customer = $this->get('volo_frontend.service.customer')->updateCustomer($customerParameters);

            return new JsonResponse([
                'html' => $this->render(
                    'VoloFrontendBundle:Checkout/Partial:contact_information_edit.html.twig',
                    [
                        'customer' => $this->get('volo_frontend.api.serializer')->normalize($customer)
                    ]
                )->getContent()
            ]);
        } catch (PhoneNumberValidationException $e) {
            return new JsonResponse([
                'invalidPhoneError' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/{vendorCode}/customer/{email}", name="checkout_edit_contact_information", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"PUT"})
     * @Template("VoloFrontendBundle:Checkout/Partial:contact_information_edit.html.twig")
     *
     * @param Request $request
     * @param $vendorCode
     *
     * @return array
     */
    public function editContactInformationAction(Request $request, $vendorCode)
    {
        if (!$this->isGranted('ROLE_CUSTOMER')) {
            return new JsonResponse([], Response::HTTP_FORBIDDEN);
        }

        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($vendorCode);
        } catch (ApiErrorException $exception) {
            throw new NotFoundHttpException(sprintf('Vendor "%s" invalid', $vendorCode), $exception);
        }
        
        try {
            $data = $this->sanitizeInputData($request->request->get('customer', []));

            $customer = $this->get('volo_frontend.service.customer')->updateCustomer($data);

            /** @var \Volo\FrontendBundle\Security\Token $token */
            $token = $this->get('security.token_storage')->getToken();
            
            try {
                if (array_key_exists('newsletter_checkbox', $data)) {
                    $this->get('volo_frontend.provider.newsletter')->subscribe(
                        $customer->getEmail(),
                        $vendor->getCity()->getId(),
                        $token->getAccessToken()
                    );
                } else {
                    $this->get('volo_frontend.provider.newsletter')->unsubscribe($token->getAccessToken());
                }                
            } catch (ApiErrorException $exception) {
                // these endpoints are throwing exceptions if you try to unsubscribe while not being subscribed
                // or the other way around, we simply ignore that.
            }


            $viewContent = $this->renderView(
                'VoloFrontendBundle:Checkout/Partial:contact_information_edit.html.twig',
                [
                    'customer' => $this->get('volo_frontend.api.serializer')->normalize($customer)
                ]
            );

            return new JsonResponse([
                'html' => $viewContent
            ]);
        } catch (PhoneNumberValidationException $e) {
            return new JsonResponse([
                'invalidPhoneError' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param Vendor $vendor
     *
     * @return array
     */
    protected function getCart(Vendor $vendor)
    {
        $session = $this->get('session');
        $cart = $this->get('volo_frontend.service.cart_manager')->getCart($session, $vendor->getId());

        if ($cart === null || count($cart['products']) === 0) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'No cart found for this vendor');
        }

        return $cart;
    }

    /**
     * @param array  $cart
     * @param Vendor $vendor
     * @param array  $data
     *
     * @return array
     */
    protected function handleOrder(array $cart, Vendor $vendor, array $data)
    {
        $orderManager = $this->get('volo_frontend.service.order_manager');

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var \Volo\FrontendBundle\Security\Token $token */
            $token          = $this->get('security.token_storage')->getToken();
            $paymentTypeId  = $data['payment_type_id'];
            $addressId      = $data['customer_address_id'];
            $expectedAmount = $data['expected_total_amount'];

            $order = $orderManager->placeOrder(
                $token->getAccessToken(),
                $addressId,
                $expectedAmount,
                $paymentTypeId,
                $cart
            );

            if ($order['hosted_payment_page_redirect'] === null) {
                $orderManager->payment($token->getAccessToken(), $data + $order);
            }
        } else {
            $session = $this->get('session');
            $guestCustomer = $session->get(static::SESSION_GUEST_CUSTOMER_KEY_TEMPLATE);
            $session->set(OrderController::SESSION_GUEST_ORDER_ACCESS_TOKEN, $guestCustomer->getAccessToken());

            $order = $orderManager->placeGuestOrder(
                $guestCustomer,
                $data['expected_total_amount'],
                $data['payment_type_id'],
                $cart
            );

            if ($order['hosted_payment_page_redirect'] === null) {
                $orderManager->guestPayment($order, $data['encrypted_payment_data'], $data['client_ip']);
            }
        }
        
        // if we're using hosted payment, at this point the order is placed but not paid.
        if ($order['hosted_payment_page_redirect'] === null) {
            $this->get('volo_frontend.service.cart_manager')->deleteCart($this->get('session'), $vendor->getId());
        }

        return $order;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function sanitizeInputData($data)
    {
        array_walk($data, function(&$value) {
            $value = filter_var($value, FILTER_SANITIZE_STRING);
        });

        return $data;
    }
}
