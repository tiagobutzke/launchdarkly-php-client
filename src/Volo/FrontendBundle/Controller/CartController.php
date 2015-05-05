<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CartController extends Controller
{
    /**
     * @Route("/cart/calculate", name="cart_calculate", methods={"POST"}, defaults={"_format": "json"})
     */
    public function calculateAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new HttpException(500);
        }

        $apiResult = $this->get('volo_frontend.service.cart_manager')->calculate($data);

        return new Response(json_encode($apiResult['data']), $apiResult['status_code']);
    }
}
