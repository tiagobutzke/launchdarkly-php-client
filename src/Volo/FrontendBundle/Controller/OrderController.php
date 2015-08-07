<?php

namespace Volo\FrontendBundle\Controller;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Entity\Order\OrderPayment;
use Foodpanda\ApiSdk\Exception\OrderNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/orders")
 */
class OrderController extends BaseController
{
    const SESSION_GUEST_ORDER_ACCESS_TOKEN = 'guest_order_access_token';
    const SESSION_ORDER_PAYMENT_CODE = 'order_payment_code';

    /**
     * @Route("/{orderCode}/tracking", name="order_tracking", options={"expose"=true})
     * @Method({"GET"})
     *
     * @param Request $request
     * @param string $orderCode
     * @return array
     */
    public function statusAction(Request $request, $orderCode)
    {
        $session = $request->getSession();
        $accessToken = $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
            ? $this->get('security.token_storage')->getToken()->getAccessToken()
            : new AccessToken($session->get(static::SESSION_GUEST_ORDER_ACCESS_TOKEN), 'bearer');
        $orderProvider = $this->get('volo_frontend.provider.order');
        try {
            $status = $orderProvider->fetchOrderStatus($orderCode, $accessToken);
            $orderPayment = $this->createOrderPayment($orderCode, $session, $status, $orderProvider, $accessToken);
        } catch (OrderNotFoundException $e) {
            throw $this->createNotFoundException('Order not found.', $e);
        }

        $viewName = 'VoloFrontendBundle:Order:status.html.twig';
        if ($request->isXmlHttpRequest() && $request->query->get('partial')) {
            $viewName = 'VoloFrontendBundle:Order:tracking_steps.html.twig';
        }

        $content = $this->renderView($viewName, [
            'order' => $orderPayment,
            'status' => $status
        ]);

        return new Response($content);
    }

    /**
     * @param string $orderCode
     * @param SessionInterface $session
     * @param array $status
     * @param string $orderProvider
     * @param AccessToken $accessToken
     *
     * @return OrderPayment
     */
    private function createOrderPayment(
        $orderCode,
        SessionInterface $session,
        array $status,
        $orderProvider,
        AccessToken $accessToken
    )
    {
        if ($session->get(static::SESSION_ORDER_PAYMENT_CODE) === 'cod') {
            $orderPayment = new OrderPayment();
            $orderPayment->setStatus('pending');
            $orderPayment->setReference('cod');
            $orderPayment->setAmount($status['total_value']);

            return $orderPayment;
        } else {
            $orderPayment = $orderProvider->fetchOrderPaymentInformation($orderCode, $accessToken);

            return $orderPayment;
        }
    }
}
