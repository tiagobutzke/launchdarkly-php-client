<?php

namespace Volo\FrontendBundle\Controller\Api;

use Foodpanda\ApiSdk\Entity\Cart\AbstractLocation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Volo\FrontendBundle\Controller\BaseController;

/**
 * @Route("/api/v1/vendors", defaults={"_format": "json"})
 */
class VendorController extends BaseController
{
    /**
     * @Route("/", name="api_vendors_list", options={"expose"=true})
     * @Method({"GET"})
     * @ParamConverter("location", converter="user_location_converter")
     *
     *
     * @param Request $request
     * @param AbstractLocation $location
     *
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function listAction(Request $request, AbstractLocation $location)
    {
        $cuisines = $request->query->get('cuisine');
        $foodCharacteristics = $request->query->get('food_characteristic');
        $includes = $request->query->get('includes', ['cuisines', 'metadata', 'food_characteristics']);

        $vendors = $this->getVendorService()->findAll(
            $location,
            $includes,
            explode(',', $cuisines),
            explode(',', $foodCharacteristics)
        );

        return new JsonResponse($this->getSerializer()->normalize($vendors));
    }
}
