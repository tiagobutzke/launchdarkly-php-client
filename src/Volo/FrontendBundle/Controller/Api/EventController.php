<?php

namespace Volo\FrontendBundle\Controller\Api;

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
        $eventMessages = $this->get('volo_frontend.service.event_service')->getActionMessages($latitude, $longitude);

        return new JsonResponse(['items' => $eventMessages]);
    }
}
