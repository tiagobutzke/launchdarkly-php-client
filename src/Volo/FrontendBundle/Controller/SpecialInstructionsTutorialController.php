<?php

namespace Volo\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/special-instructions-tutorial")
 */
class SpecialInstructionsTutorialController extends BaseController
{
    /**
     * @Route("/dismiss", name="special_instructions_tutorial_dismiss", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"PUT"})
     *
     * @return JsonResponse
     */
    public function dismissAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $customer = $this->getToken()->getAttribute('customer');
            $this->get('volo_frontend.service.special_instructions_tutorial')->disableTutorialForCustomer($customer);
        }
        $response = new JsonResponse(null, Response::HTTP_NO_CONTENT);
        $this->get('volo_frontend.service.special_instructions_tutorial')->disableTutorialForGuest($response);

        return $response;
    }
}
