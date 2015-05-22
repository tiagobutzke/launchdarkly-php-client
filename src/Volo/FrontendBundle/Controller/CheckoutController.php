<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($vendorCode);
        } catch (ApiErrorException $exception) {
            throw new NotFoundHttpException('Vendor invalid', $exception);
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            $address = $request->request->get('customer_address');
            $address['city_id'] = $vendor->getCity()->getId();

            $this->get('session')->set(sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode), $address);
            
            return $this->redirect($this->generateUrl('checkout_contact_information', ['vendorCode' => $vendorCode]));
        }

        return [
            'cart'   => $this->getCart($vendor),
            'vendor' => $vendor,
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
        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($vendorCode);
        } catch (ApiErrorException $exception) {
            throw new NotFoundHttpException('Vendor invalid', $exception);
        }

        if (!$this->get('session')->has(sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode))) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'No contact information found');
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            $this->get('session')->set(
                sprintf(static::SESSION_CONTACT_KEY_TEMPLATE, $vendorCode),
                $request->request->get('customer')
            );

            return $this->redirect($this->generateUrl('checkout_payment', ['vendorCode' => $vendorCode]));
        }

        return [
            'cart'             => $this->getCart($vendor),
            'vendor'           => $vendor,
            'customer_address' => $this->get('session')->get(
                sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode)
            ),
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

        if (!$session->has(sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode))
            || !$session->has(sprintf(static::SESSION_CONTACT_KEY_TEMPLATE, $vendorCode))
        ) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'No contact information found');
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            $guestCustomer = $this->get('volo_frontend.service.customer')->createGuestCustomer(
                $session->get(sprintf(static::SESSION_CONTACT_KEY_TEMPLATE, $vendorCode)),
                $session->get(sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode))
            );

            $order         = $this->get('volo_frontend.service.order_manager')->placeOrder($guestCustomer, $cart);
            $encryptedData = $request->request->get('adyen-encrypted-data');

            $this->get('volo_frontend.service.order_manager')->pay($order, $encryptedData);

            return $this->redirect($this->generateUrl('checkout_success', ['orderCode' => $order['code']]));
        }
        
        $configuration = $this->get('volo_frontend.service.configuration')->getConfiguration();

        return [
            'cart'             => $cart,
            'vendor'           => $vendor,
            'customer_address' => $session->get(sprintf(static::SESSION_DELIVERY_KEY_TEMPLATE, $vendorCode)),
            'customer'         => $session->get(sprintf(static::SESSION_CONTACT_KEY_TEMPLATE, $vendorCode)),
            'adyen_public_key' => $configuration->getAdyenEncryptionPublicKey(),
        ];
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
        $order = $this->get('volo_frontend.provider.order')->orderPaymentInformation($orderCode);
        
        return [
            'order' => $order
        ];
    }

    /**
     * @param Vendor $vendor
     *
     * @return array
     */
    protected function getCart(Vendor $vendor)
    {
        $cartManager = $this->get('volo_frontend.service.cart_manager');

        $session = $this->get('session')->getId();
        
        $cart = $cartManager->getCart($session, $vendor->getId());

        if ($cart === null) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'No cart found for this vendor');
        }

        return $cartManager->calculateCart($cart);
    }
}
