<?php

namespace Volo\FrontendBundle\Controller;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Entity\Address\Address;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Volo\FrontendBundle\Security\Token;
use Volo\FrontendBundle\Service\CustomerLocationService;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;

/**
 * @Route("/checkout")
 */
class CheckoutController extends Controller
{
    const SESSION_DELIVERY_KEY_TEMPLATE = 'checkout-%s-delivery';
    const SESSION_CONTACT_KEY_TEMPLATE  = 'checkout-%s-contact';
    
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
            $address = $request->request->get('customer_address');

            $request->getSession()->set($sessionDeliveryKey, $address);
            
            return $this->redirect($this->generateUrl('checkout_contact_information', ['vendorCode' => $vendorCode]));
        }

        $address = $request->getSession()->get($sessionDeliveryKey, []);
        $customerLocationService = $this->get('volo_frontend.service.customer_location');
        $defaultAddress = [
            'postcode' => $customerLocationService->get($request->getSession()->getId())[CustomerLocationService::KEY_PLZ],
            'city' => $vendor->getCity()->getName()
        ];
        $cartManager = $this->get('volo_frontend.service.cart_manager');
        return [
            'cart'             => $cartManager->calculateCart($this->getCart($vendor)),
            'customer_address' => $defaultAddress + $address,
            'vendor'           => $vendor,
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

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('checkout_payment', ['vendorCode' => $vendorCode]);
        }

        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($vendorCode);
        } catch (ApiErrorException $exception) {
            throw new NotFoundHttpException('Vendor invalid', $exception);
        }

        if (!$this->get('session')->has(sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode))) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'No contact information found');
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            $customerData = $request->request->get('customer');
            try {
                $parsedNumber = $this->get('volo_frontend.service.phone_number')
                    ->parsePhoneNumber($customerData['mobile_number']);

                $customerData['mobile_number'] = $parsedNumber->getNationalNumber();
                $customerData['mobile_country_code'] = '+' . $parsedNumber->getCountryCode();
            } catch (PhoneNumberValidationException $e) {
                $errorMessages[] = $this->get('translator')->trans(sprintf('%s: %s', 'Phone number', $e->getMessage()));
            }

            if (count($errorMessages) === 0) {
                $this->get('session')->set(
                    sprintf(static::SESSION_CONTACT_KEY_TEMPLATE, $vendorCode),
                    $customerData
                );

                return $this->redirect($this->generateUrl('checkout_payment', ['vendorCode' => $vendorCode]));
            }
        }

        $cart = $this->getCart($vendor);
        return [
            'errorMessages'    => $errorMessages,
            'cart'             => $this->get('volo_frontend.service.cart_manager')->calculateCart($cart),
            'vendor'           => $vendor,
            'customer_address' => $this->get('session')->get(
                sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode)
            ),
            'customer' => $this->get('session')->get(sprintf(static::SESSION_CONTACT_KEY_TEMPLATE, $vendorCode)),
        ];
    }

    /**
     * TODO: Js is hardcoded in the view, extract it
     * TODO: Error handling would be nice
     * TODO: Create success page and redirect to it
     *
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

        if ($request->getMethod() === Request::METHOD_POST) {
            return $this->handleOrder($cart, $vendor, $request);
        }

        $configuration = $this->get('volo_frontend.service.configuration')->getConfiguration();

        $viewData = [
            'cart'             => $this->get('volo_frontend.service.cart_manager')->calculateCart($cart),
            'vendor'           => $vendor,
            'adyen_public_key' => $configuration->getAdyenEncryptionPublicKey(),
        ];

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var \Volo\FrontendBundle\Security\Token $token */
            $token            = $this->get('security.token_storage')->getToken();
            $serializer       = $this->get('volo_frontend.api.serializer');
            $customerProvider = $this->get('volo_frontend.provider.customer');

            $addresses = $customerProvider->getAddresses($token->getAccessToken());

            $customerLocationService = $this->get('volo_frontend.service.customer_location');
            $viewData['default_address'] = [
                'postcode' => $customerLocationService->get($session->getId())[CustomerLocationService::KEY_PLZ],
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
     * @Route("/success/{orderCode}", name="checkout_success")
     * @Method({"GET"})
     * @Template()
     *
     * @param string $orderCode
     *
     * @return array
     */
    public function successAction($orderCode)
    {
        $accessToken = $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
            ? $this->get('security.token_storage')->getToken()->getAccessToken()
            : null;

        $order = $this->get('volo_frontend.provider.order')->orderPaymentInformation($orderCode, $accessToken);

        return [
            'order' => $order
        ];
    }

    /**
     * @Route("/checkout/create_address", name="checkout_create_address", options={"expose"=true})
     * @Method({"POST"})
     * @Template("VoloFrontendBundle:Checkout/Partial:delivery_information_list.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAddressAction(Request $request)
    {
        $serializer  = $this->get('volo_frontend.api.serializer');
        $accessToken = $this->get('security.token_storage')->getToken()->getAccessToken();
        $address     = $serializer->denormalize($request->request->get('customer_address'), Address::class);

        $this->get('volo_frontend.provider.customer')->createAddress($accessToken, $address);
        $addresses = $this->get('volo_frontend.provider.customer')->getAddresses($accessToken);

        return new JsonResponse($serializer->normalize($addresses)['items']);
    }

    /**
     * @Route("/checkout/edit_contact_information", name="checkout_edit_contact_information", options={"expose"=true})
     * @Method({"POST"})
     * @Template("VoloFrontendBundle:Checkout/Partial:contact_information_edit.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function editContactInformationAction(Request $request)
    {
        $customer = $this->get('volo_frontend.service.customer')->updateCustomer($request->request->get('customer'));

        return [
            'customer' => $this->get('volo_frontend.api.serializer')->normalize($customer),
        ];
    }

    /**
     * @param Vendor $vendor
     *
     * @return array
     */
    protected function getCart(Vendor $vendor)
    {
        $session = $this->get('session')->getId();
        $cart = $this->get('volo_frontend.service.cart_manager')->getCart($session, $vendor->getId());

        if ($cart === null) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'No cart found for this vendor');
        }

        return $cart;
    }

    /**
     * @param         $cart
     * @param Vendor  $vendor
     * @param Request $request
     *
     * @return RedirectResponse
     */
    protected function handleOrder($cart, Vendor $vendor, Request $request)
    {
        $orderManager = $this->get('volo_frontend.service.order_manager');

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var \Volo\FrontendBundle\Security\Token $token */
            $token       = $this->get('security.token_storage')->getToken();
            $customerAddressId = $request->request->get('customer_address_id');
            $order             = $orderManager->placeOrder($token->getAccessToken(), $customerAddressId, $cart);

            switch (true) {
                case $request->request->has('adyen_encrypted_data'):
                    $order['encrypted_payment_data'] = $request->request->get('adyen_encrypted_data');
                    break;

                case $request->request->has('credit_card_id'):
                    $order['credit_card_id'] = $request->request->get('credit_card_id');
                    break;

                default:
                    throw new HttpException(
                        Response::HTTP_BAD_REQUEST,
                        'No recurring or CSE payment information provided'
                    );
            }

            $orderManager->payment($token->getAccessToken(), $order);
        } else {
            $session = $this->get('session');
            $guestCustomer = $this->get('volo_frontend.service.customer')->createGuestCustomer(
                $session->get(sprintf(static::SESSION_CONTACT_KEY_TEMPLATE, $vendor->getCode())),
                $session->get(sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendor->getCode()))
            );
            $order         = $orderManager->placeGuestOrder($guestCustomer, $cart);

            $encryptedData = $request->request->get('adyen_encrypted_data');
            $orderManager->guestPayment($order, $encryptedData);
        }

        $this->get('volo_frontend.service.cart_manager')->deleteCart($this->get('session')->getId(), $vendor->getId());

        return $this->redirect($this->generateUrl('checkout_success', ['orderCode' => $order['code']]));
    }
}
