<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator;

/**
 * @Route("/cart")
 */
class CartController extends BaseController
{
    /**
     * @Route("/calculate", name="cart_calculate", methods={"POST"}, defaults={"_format": "json"}, options={"expose"=true})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function calculateAction(Request $request)
    {
        $data = $this->decodeJsonContent($request);

        $cartManager = $this->get('volo_frontend.service.cart_manager');
        try {
            $apiResult = $cartManager->calculateCart($data);
            $cartManager->saveCart($request->getSession(), $data['vendor_id'], $data);
        } catch (ApiErrorException $e) {
            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        }

        return new JsonResponse($apiResult);
    }

    /**
     * @Route(
     *      "/default-cart", name="default_cart_values",
     *      defaults={"_format": "json"},
     *      options={"expose"=true},
     *      condition="request.isXmlHttpRequest()"
     * )
     * @return JsonResponse
     */
    public function getDefaultCartValuesAction()
    {
        $cart = $this->get('volo_frontend.service.cart_manager')->getDefaultCart($this->get('session'));

        return new JsonResponse(
            [
                'vendor_id'      => ($cart === null || !array_key_exists('vendor_id', $cart)) ? '' : $cart['vendor_id'],
                'products_count' => $cart === null ? 0 : array_sum(array_column($cart['products'], 'quantity')),
            ]
        );
    }
}
