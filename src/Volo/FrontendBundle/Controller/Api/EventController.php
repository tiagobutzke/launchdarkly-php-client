<?php

namespace Volo\FrontendBundle\Controller\Api;

use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Volo\FrontendBundle\Controller\BaseController;

/**
 * @Route("/api/v1/events", defaults={"_format": "json"})
 */
class EventController extends BaseController
{
    /**
     * @Route(
     *      "/lat/{latitude}/lng/{longitude}",
     *      requirements={
     *          "latitude"="-?(\d*[.])?\d+",
     *          "longitude"="-?(\d*[.])?\d+"
     *      },
     *      name="api_events_get",
     *      options={"expose"=true},
     *      condition="request.isXmlHttpRequest()"
     * )
     * @Method({"GET"})
     *
     * @param float $latitude
     * @param float $longitude
     *
     * @return JsonResponse
     */
    public function listAction($latitude, $longitude)
    {
        $location = new GpsLocation($latitude, $longitude);
        $eventMessages = $this->get('volo_frontend.service.event_service')->getActionMessages($location);

        return new JsonResponse(['items' => $eventMessages]);
    }

    /**
     * @Route(
     *      "/{vendorId}/lat/{latitude}/lng/{longitude}",
     *      requirements={
     *          "vendorId"="\d+",
     *          "latitude"="-?(\d*[.])?\d+",
     *          "longitude"="-?(\d*[.])?\d+"
     *      },
     *      name="api_events_get_by_vendor",
     *      options={"expose"=true},
     *      condition="request.isXmlHttpRequest()"
     * )
     * @Method({"GET"})
     *
     * @param int $vendorId
     * @param float $latitude
     * @param float $longitude
     *
     * @return JsonResponse
     */
    public function vendorAction($vendorId, $latitude, $longitude)
    {
        $location = new GpsLocation($latitude, $longitude);
        $eventMessages = $this->get('volo_frontend.service.event_service')->getActionMessagesByVendor($vendorId, $location);

        return new JsonResponse(['items' => $eventMessages]);
    }
}
