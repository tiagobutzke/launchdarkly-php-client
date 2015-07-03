<?php

namespace Volo\FrontendBundle\Controller;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Validator\Validator;
use Symfony\Component\HttpFoundation\Request;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/payment-gateway")
 */
class PaymentGatewayController extends Controller
{
    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/handle-payment/{orderCode}", name="paypal_handle_payment", methods={"get"},
     *      requirements={"code": "([A-Za-z][A-Za-z0-9]{3})-([A-Za-z][A-Za-z0-9]{3})"}
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

        if ($request->query->get('success')) {
            $this->get('volo_frontend.service.cart_manager')->deleteCart(
                $request->getSession(),
                $vendor->getId()
            );

            $response = new RedirectResponse(
                $this->generateUrl('order_tracking', ['orderCode' => $orderCode]),
                HTTP_REDIRECT_FOUND
            );
            $response->headers->setCookie(new Cookie('orderPay', 'true'));

            return $response;
        } else {
            $this->addFlash('paypal-error', 'paypal.payment_error');

            return $this->redirectToRoute('checkout_payment', ['vendorCode' => $vendor->getCode()]);
        }
    }
}
