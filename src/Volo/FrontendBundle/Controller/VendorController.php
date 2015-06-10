<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Http\JsonErrorResponse;
use Volo\FrontendBundle\Service\CustomerLocationService;

/**
 * @Route("/restaurant")
 */
class VendorController extends Controller
{
    /**
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
        } catch (ApiErrorException $exception) {
            throw $this->createNotFoundException('Vendor not found!', $exception);
        }

        $cart = $this->get('volo_frontend.service.cart_manager')->getCartIfDefault(
            $request->getSession()->getId(),
            $vendor->getId()
        );

        $location = $this->get('volo_frontend.service.customer_location')->get($request->getSession());
        $isDeliverable = is_array($location) ? $this->get('volo_frontend.service.deliverability')
            ->isDeliverableLocation(
                $vendor->getId(),
                $location[CustomerLocationService::KEY_LAT],
                $location[CustomerLocationService::KEY_LNG]
            ) : false;

        if ($cart) {
            $cartManager = $this->get('volo_frontend.service.cart_manager');

            try {
                $cart = $cartManager->calculateCart($cart);
            } catch (ApiErrorException $exception) {
                $cart = null;
            }

            if ($cart && !$isDeliverable) {
                $cart = null;
            }
        }

        return [
            'vendor'        => $vendor,
            'cart'          => $cart,
            'address'       => is_array($location) ? $location[CustomerLocationService::KEY_ADDRESS] : '',
            'location'      => [
                'type'      => 'polygon',
                'latitude'  => $isDeliverable ? $location[CustomerLocationService::KEY_LAT] : null,
                'longitude' => $isDeliverable ? $location[CustomerLocationService::KEY_LNG] : null
            ],
            'isDeliverable' => $isDeliverable
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

    /**
     * @Route("/{code}", name="vendor_by_code", requirements={"code": "([A-Za-z][A-Za-z0-9]{3})"})
     * @Method({"GET"})
     *
     * @param string $code
     *
     * @return array
     */
    public function vendorByCodeAction($code)
    {
        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($code);
        } catch (ApiErrorException $exception) {
            throw $this->createNotFoundException('Vendor not found!', $exception);
        }

        return $this->redirectToVendorPage($code, $vendor->getUrlKey());
    }

    /**
     * @Route("/{urlKey}", name="vendor_by_url_key")
     * @Method({"GET"})
     *
     * @param string $urlKey
     *
     * @return array
     */
    public function vendorByUrlKeyAction($urlKey)
    {
        try {
            $code = $this->get('volo_frontend.service.vendor')->getVendorCodeByUrlKey($urlKey);
        } catch (\RuntimeException $e) {
            throw $this->createNotFoundException('Vendor not found!', $e);
        }

        return $this->redirectToVendorPage($code, $urlKey);
    }

    /**
     * @param string $code
     * @param string $urlKey
     *
     * @return RedirectResponse
     */
    protected function redirectToVendorPage($code, $urlKey)
    {

        return $this->redirectToRoute(
            'vendor',
            ['code' => $code, 'urlKey' => $urlKey],
            Response::HTTP_FOUND
        );
    }
}
