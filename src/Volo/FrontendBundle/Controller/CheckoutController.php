<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Volo\FrontendBundle\Service\CustomerLocationService;

class CheckoutController extends Controller
{
    /**
     * @Route("/checkout/{vendorCode}", name="checkout")
     * @Template()
     *
     * @param Request $request
     * @param $vendorCode
     *
     * @return array
     */
    public function indexAction(Request $request, $vendorCode)
    {
        $deliveryAddress = $this->get('volo_frontend.service.customer_location')->get($request->getSession()->getId());

        return [
            'cart' => json_encode(
                $this->get('volo_frontend.service.cart_manager')->getCart(
                    $request->getSession()->getId(),
                    $vendorCode
                )
            ),
            'shippingAddress' => [
                'formatted' => $deliveryAddress[CustomerLocationService::KEY_FORMATTED_ADDRESS],
                'lat' => $deliveryAddress[CustomerLocationService::KEY_LAT],
                'lng' => $deliveryAddress[CustomerLocationService::KEY_LNG]
            ]
        ];
    }
}
