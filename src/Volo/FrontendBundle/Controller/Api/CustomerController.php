<?php

namespace Volo\FrontendBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;

/**
 * @Route("/api/v1/customers", defaults={"_format": "json"}, condition="request.isXmlHttpRequest()")
 */
class CustomerController extends BaseApiController
{
    /**
     * @Route("/{id}", name="api_customers_update", options={"expose"=true})
     * @Method({"PUT"})
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function updateContactInformationAction(Request $request, $id)
    {
        $this->isCustomerAllowed($id);

        try {
            $data = $this->decodeJsonContent($request);

            $customer = $this->get('volo_frontend.service.customer')->updateCustomer($data);

        } catch (PhoneNumberValidationException $e) {
            return new JsonResponse([
                'invalidPhoneError' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        $customer->setPassword('');
        $customer->setToken('');

        return new JsonResponse($this->getSerializer()->normalize($customer));
    }
}
