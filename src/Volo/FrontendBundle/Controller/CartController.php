<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator;
use Volo\FrontendBundle\Http\JsonErrorResponse;

/**
 * @Route("/cart")
 */
class CartController extends Controller
{
    /**
     * @Route("/calculate", name="cart_calculate", methods={"POST"}, defaults={"_format": "json"})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function calculateAction(Request $request)
    {
        $content = $request->getContent();

        if('' === $content){
            throw new BadRequestHttpException('Content is empty.');
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Content is not a valid json.');
        }

        try {
            $apiResult = $this->get('volo_frontend.service.cart_manager')->calculate($data);
        } catch (ApiErrorException $e) {
            return new JsonErrorResponse($e);
        }

        return new JsonResponse($apiResult);
    }
}
