<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class VendorController extends Controller
{
    /**
     * @Route(
     *      "/restaurant/{code}/{slug}",
     *      name="vendor",
     *      requirements={
     *          "code": "([A-Za-z][A-Za-z0-9]{3})"
     *      }
     * )
     * @Template()
     * @Method({"GET"})
     *
     * @param Request $request
     * @param string $code
     *
     * @return array
     */
    public function vendorAction(Request $request, $code)
    {
        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($code);
        } catch (EntityNotFoundException $exception) {
            throw $this->createNotFoundException('Vendor not found!', $exception);
        }

        $cart = $this->get('volo_frontend.service.cart_manager')->getCart(
            $request->getSession()->getId(),
            $vendor->getId()
        );

        if ($cart) {
            $cartManager = $this->get('volo_frontend.service.cart_manager');
            $cart = $cartManager->calculateCart($cart);
        }

        return [
            'vendor' => $vendor,
            'cart' => $cart
        ];

    }
}
