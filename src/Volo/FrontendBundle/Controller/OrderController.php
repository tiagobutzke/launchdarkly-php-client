<?php

namespace Volo\FrontendBundle\Controller;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Exception\OrderNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/order")
 */
class OrderController extends Controller
{
    const SESSION_GUEST_ORDER_ACCESS_TOKEN = 'guest_order_access_token';

    /**
     * @Route("/{orderCode}/tracking", name="order_tracking", options={"expose"=true})
     * @Method({"GET"})
     * @Template()
     *
     * @param string $orderCode
     *
     * @return array
     */
    public function statusAction($orderCode)
    {
        $session = $this->get('session');
        $accessToken = $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
            ? $this->get('security.token_storage')->getToken()->getAccessToken()
            : new AccessToken($session->get(static::SESSION_GUEST_ORDER_ACCESS_TOKEN), 'bearer');

        try {
            $order = $this->get('volo_frontend.provider.order')->orderPaymentInformation($orderCode, $accessToken);
            $status = $this->get('volo_frontend.provider.order')->fetchOrderStatus($orderCode, $accessToken);
        } catch (OrderNotFoundException $e) {
            throw $this->createNotFoundException('Order not found.', $e);
        }

        return [
            'order' => $order,
            'status' => $status,
        ];
    }
}
