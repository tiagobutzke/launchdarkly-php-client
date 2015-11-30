<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Volo\FrontendBundle\Service\CustomerLocationService;

/**
 * @Route("/restaurant")
 */
class VendorController extends BaseController
{
    /**
     * @Route(
     *      "/{code}/{urlKey}",
     *      name="vendor",
     *      options={"expose"=true},
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
        $vendorCode = strtolower($code);
        $customerLocationService = $this->getCustomerLocationService();
        $customerLocation = $customerLocationService->get($request->getSession());
        $vendorService = $this->getVendorService();

        try {
            $vendor = $vendorService->findById($vendorCode);

            if ($vendorCode !== $code || $vendor->getUrlKey() !== $urlKey) {
                return $this->redirectToVendorPage($vendor->getCode(), $vendor->getUrlKey());
            }

            $isDeliverable = is_array($customerLocation) ? $this->get('volo_frontend.service.deliverability')
                ->isDeliverableLocation(
                    $vendor->getId(),
                    $customerLocation[CustomerLocationService::KEY_LAT],
                    $customerLocation[CustomerLocationService::KEY_LNG]
                ) : false;

            if ($isDeliverable) {
                $vendorService->attachMetaData(
                    $vendor,
                    $customerLocation[CustomerLocationService::KEY_LAT],
                    $customerLocation[CustomerLocationService::KEY_LNG]
                );
            }
        } catch (ApiErrorException $exception) {
            throw $this->createNotFoundException('Vendor not found!', $exception);
        }

        $cart = $this->get('volo_frontend.service.cart_manager')->getCartIfDefault(
            $request->getSession(),
            $vendor->getId()
        );

        $formattedLocation = $customerLocationService->format($customerLocation);

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

        $specialInstructionsService = $this->get('volo_frontend.service.special_instructions_tutorial');
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $customer = $this->getToken()->getAttribute('customer');
            $specialInstructionsTutorialEnabled = $specialInstructionsService->isTutorialEnabledForCustomer($customer);
            if ($specialInstructionsTutorialEnabled
                && !$specialInstructionsService->isTutorialEnabledForGuest($request)) {
                $specialInstructionsService->disableTutorialForCustomer($customer);
                $specialInstructionsTutorialEnabled = false;
            }
        } else {
            $specialInstructionsTutorialEnabled = $specialInstructionsService->isTutorialEnabledForGuest($request);
        }

        $address = is_array($customerLocation) ? $customerLocation[CustomerLocationService::KEY_PLZ] : '';

        return [
            'vendor'            => $vendor,
            'cart'              => $cart,
            'address'           => $address,
            'location'          => $customerLocation,
            'formattedLocation' => $formattedLocation,
            'isDeliverable'     => $isDeliverable,
            // Temporary disabled
            'showSpecialInstructionsTutorial' => false && $specialInstructionsTutorialEnabled
        ];
    }

    /**
     * @Route(
     *      "/{vendorId}/delivery-check/lat/{latitude}/lng/{longitude}",
     *      name="vendor_delivery_validation_by_gps",
     *      options={"expose"=true},
     *      condition="request.isXmlHttpRequest()"
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
            return $this->get('volo_frontend.service.api_error_translator')->createJsonErrorResponse($e);
        }
    }

    /**
     * @Route("/{id}", name="vendor_by_id", options={"expose"=true}, requirements={"id": "^([0-9]+)$"})
     * @Method({"GET"})
     *
     * @param int $id
     *
     * @return array
     */
    public function vendorByIdAction($id)
    {
        try {
            $vendorIdentifierCache = $this->get('volo_frontend.service.vendor')->getCachedVendorCodeById($id);
        } catch (\RuntimeException $e) {
            throw $this->createNotFoundException('Vendor not found!', $e);
        }

        return $this->redirectToVendorPage($vendorIdentifierCache['code'], $vendorIdentifierCache['urlKey']);
    }

    /**
     * @Route("/{code}", name="vendor_by_code", requirements={"code": "(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]{4}"})
     * @Method({"GET"})
     *
     * @param string $code
     *
     * @return array
     */
    public function vendorByCodeAction($code)
    {
        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find(strtolower($code));
        } catch (ApiErrorException $exception) {
            throw $this->createNotFoundException('Vendor not found!', $exception);
        }

        return $this->redirectToVendorPage($vendor->getCode(), $vendor->getUrlKey());
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
            $code = $this->get('volo_frontend.service.vendor')->getCachedVendorCodeByUrlKey($urlKey);
        } catch (\RuntimeException $e) {
            throw $this->createNotFoundException('Vendor not found!', $e);
        }

        return $this->vendorByCodeAction($code);
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
            ['code' => $code, 'urlKey' => $urlKey]
        );
    }
}
