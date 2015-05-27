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
     *      "/restaurant/{code}/{urlKey}",
     *      name="vendor",
     *      requirements={
     *          "code": "([A-Za-z][A-Za-z0-9]{3})",
     *          "urlKey": "[a-z-]+"
     *      }
     * )
     * @Template()
     * @Method({"GET"})
     *
     * @param Request $request
     * @param string $code
     * @param string $urlKey
     *
     * @return array
     */
    public function vendorAction(Request $request, $code, $urlKey)
    {
        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($code);
            if ($vendor->getUrlKey() !== $urlKey) {
                return $this->redirectToRoute('vendor', [
                    'code' => $vendor->getCode(),
                    'urlKey' => $vendor->getUrlKey()
                ]);
            }
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
