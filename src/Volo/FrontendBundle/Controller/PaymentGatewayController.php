<?php

namespace Volo\FrontendBundle\Controller;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Exception\OrderNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator;
use Symfony\Component\HttpFoundation\Request;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/payment-gateway")
 */
class PaymentGatewayController extends BaseController
{
    const ORDER_PAID_STATUS = 'paid';
    
    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/handle-payment/{orderCode}", name="handle_payment", methods={"get"}, options={"expose"=true},
     *      requirements={"orderCode": "([A-Za-z][A-Za-z0-9]{3})-([A-Za-z][A-Za-z0-9]{3})"}
     * )
     * @param Request $request
     * @param string  $orderCode
     *
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function handlePaymentAction(Request $request, $orderCode)
    {
        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find(current(explode('-', $orderCode)));
        } catch (ApiErrorException $exception) {
            throw new NotFoundHttpException('Vendor invalid', $exception);
        }

        $accessToken = $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
            ? $this->get('security.token_storage')->getToken()->getAccessToken()
            : new AccessToken($request->getSession()->get(OrderController::SESSION_GUEST_ORDER_ACCESS_TOKEN), 'bearer');

        $this->get('volo_frontend.provider.order')->handleHostedPayment(
            $accessToken,
            $orderCode,
            $request->query->all(),
            $request->request->all()
        );

        if ($this->isPaymentSucessful($request, $orderCode)) {
            $this->get('volo_frontend.service.cart_manager')->deleteCart(
                $request->getSession(),
                $vendor->getId()
            );

            $response = $this->redirectToRoute('order_tracking', ['orderCode' => $orderCode]);
            $response->headers->setCookie(new Cookie('orderPay', 'true', 0, '/', null, $request->isSecure(), false));

            return $response;
        } else {
            $this->addFlash('payment_error', $this->getErrorMessage($request));

            return $this->redirectToRoute('checkout_payment', ['vendorCode' => $vendor->getCode()]);
        }
    }

    /**
     * @param Request $request
     * @param string  $orderCode
     *
     * @return mixed
     */
    protected function isPaymentSucessful(Request $request, $orderCode)
    {
        switch (true) {
            case $request->query->has('success'): // paypal
                if (!$request->query->get('success')) {
                    return false;
                }

                $session     = $this->get('session');
                $accessToken = $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
                    ? $this->get('security.token_storage')->getToken()->getAccessToken()
                    : new AccessToken($session->get(OrderController::SESSION_GUEST_ORDER_ACCESS_TOKEN), 'bearer');

                try {
                    $order = $this->get('volo_frontend.provider.order')->orderPaymentInformation($orderCode,
                        $accessToken);

                    return $order['status'] === static::ORDER_PAID_STATUS;
                } catch (OrderNotFoundException $e) {
                    return false;
                }
                break;
            case $request->query->has('paymentMethod') && $request->query->has('authResult'): // adyen hpp
                return $request->query->get('authResult') === 'AUTHORISED';
                break;
            default:
                return false;
        }
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    protected function getErrorMessage(Request $request)
    {
        switch (true) {
            case $request->query->has('success'): // paypal
                return 'paypal.payment_error';
                break;
            case $request->query->has('paymentMethod') && $request->query->has('authResult'): // adyen hpp
                return sprintf('%s.payment_error', $request->query->get('paymentMethod'));
                break;
            case $request->query->has('authResult'): // adyen hpp cancel doesn't provide payment method field
                return 'general.payment_error';
                break;
            default:
                throw new BadRequestHttpException('Payment method not recognized');
        }
    }
}
