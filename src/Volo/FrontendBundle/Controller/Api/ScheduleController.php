<?php

namespace Volo\FrontendBundle\Controller\Api;

use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Volo\FrontendBundle\Controller\BaseController;

/**
 * @Route("/api/v1/schedules", defaults={"_format": "json"})
 */
class ScheduleController extends BaseController
{
    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_timepicker_get", options={"expose"=true})
     * @Method({"GET"})
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function timepickerValuesAction($id)
    {
        try {
            $vendor = $this->get('volo_frontend.provider.vendor')->find($id);
        } catch (ApiErrorException $exception) {
            throw $this->createNotFoundException(sprintf('Vendor %s not found!', $id), $exception);
        }

        return new JsonResponse($this->get('volo_frontend.service.schedule')->getTimePickerJsonValues($vendor));
    }
}
