<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Http\JsonErrorResponse;

/**
 * @Route("/restaurant")
 */
class VendorController extends Controller
{
    /**
     * @Route("/{code}", name="vendor_by_code")
     * @Route(
     *      "/{code}/{urlKey}",
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
     * @param string $urlKey
     *
     * @return array
     */
    public function vendorAction(Request $request, $code, $urlKey = null)
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

            try {
                $cart = $cartManager->calculateCart($cart);
            } catch (ApiErrorException $exception) {
                $cart = null;
            }
        }

        return [
            'vendor' => $vendor,
            'cart' => $cart
        ];
    }

    /**
     * @Route(
     *      "/{vendorId}/delivery-check/lat/{latitude}/lng/{longitude}",
     *      name="vendor_delivery_validation_by_gps",
     *      options={"expose"=true},
     *      condition="request.isXmlHttpRequest()"
     *
     * )
     * @Method({"GET"})
     *
     * @param int $vendorId
     * @param double $latitude
     * @param double $longitude
     *
     * @return JsonResponse
     */

    public function isDeliverableAction($vendorId, $latitude, $longitude)
    {
        try {
            $result = $this->get('volo_frontend.service.deliverability')
                ->isDeliverableLocation($vendorId, $latitude, $longitude);

            return new JsonResponse(['result' => $result]);
        } catch (ApiErrorException $e) {
            return new JsonErrorResponse($e);
        }
    }
}
