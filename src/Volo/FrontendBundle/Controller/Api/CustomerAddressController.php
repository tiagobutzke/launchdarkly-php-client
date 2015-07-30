<?php

namespace Volo\FrontendBundle\Controller\Api;

use Foodpanda\ApiSdk\Entity\Address\Address;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Volo\FrontendBundle\Service\CustomerService;

/**
 * @Route("/api/v1/customer/{customerId}", defaults={"_format": "json"}, condition="request.isXmlHttpRequest()")
 */
class CustomerAddressController extends BaseApiController
{
    /**
     * @Route("/address", name="api_customers_address_list", options={"expose"=true})
     * @Method({"GET"})
     *
     * @param Request $request
     * @param int $customerId
     *
     * @return JsonResponse
     */
    public function listAction(Request $request, $customerId)
    {
        $this->isCustomerAllowed($customerId);

        $vendorId = $request->query->get('vendorId');

        $serializer = $this->getSerializer();
        $accessToken = $this->getToken()->getAccessToken();
        $addresses = $this->getCustomerService()->getAddresses($accessToken, $vendorId);

        return new JsonResponse($serializer->normalize($addresses));
    }

    /**
     * @Route("/address/{id}", name="api_customers_address_find_one")
     * @Method({"GET"})
     *
     * @param Request $request
     * @param int $customerId
     * @param int $id
     *
     * @return JsonResponse
     */
    public function findOneAction(Request $request, $customerId, $id)
    {
        $this->isCustomerAllowed($customerId);

        $vendorId = $request->query->get('vendorId');

        $serializer = $this->getSerializer();
        $accessToken = $this->getToken()->getAccessToken();

        try {
            $address = $this->getCustomerService()->getAddress($id, $accessToken, $vendorId);
        } catch (ApiErrorException $e) {
            $data = json_decode($e->getJsonErrorMessage(), true);
            if (isset($data['data']['exception_type'])
                && 'ApiObjectDoesNotExistException' === $data['data']['exception_type']) {

                throw $this->createNotFoundException(sprintf('Address not found: "%s"', $id));
            }

            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        }

        return new JsonResponse($serializer->normalize($address));
    }

    /**
     * @Route("/address/{id}", name="api_customers_address_update")
     * @Method({"PUT"})
     *
     * @param Request $request
     * @param int $customerId
     * @param int $id
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, $customerId, $id)
    {
        $this->isCustomerAllowed($customerId);

        $data = $this->decodeJsonContent($request);
        $accessToken = $this->getToken()->getAccessToken();
        $address = $this->getSerializer()->denormalizeCustomerAddress($data);

        try {
            $newAddress = $this->getCustomerService()->updateAddress($address, $accessToken);
        } catch (ApiErrorException $e) {
            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        }

        return new JsonResponse($this->getSerializer()->normalize($newAddress));
    }

    /**
     * @Route("/address/{id}", name="api_customers_address_delete")
     * @Method({"DELETE"})
     *
     * @param int $customerId
     * @param int $id
     *
     * @return JsonResponse
     */
    public function deleteAction($customerId, $id)
    {
        $this->isCustomerAllowed($customerId);

        $accessToken = $this->getToken()->getAccessToken();

        try {
            $this->getCustomerService()->deleteAddress($id, $accessToken);
        } catch (ApiErrorException $e) {
            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        }

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/address", name="api_customers_address_create")
     * @Method({"POST"})
     *
     * @param Request $request
     * @param int $customerId
     *
     * @return JsonResponse
     */
    public function createAction(Request $request, $customerId)
    {
        $this->isCustomerAllowed($customerId);

        $vendorId    = $request->request->get('vendor_id');
        $serializer = $this->getSerializer();
        $token = $this->getToken();
        $accessToken = $token->getAccessToken();

        $data = $this->sanitizeInputData($this->decodeJsonContent($request));

        /** @var Address $address */
        $address = $serializer->denormalize($data, Address::class);
        try {
            $address = $this->getCustomerService()->create($address, $accessToken);
        } catch (ApiErrorException $e) {
            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        }

        $url = $this->generateUrl(
            'api_customers_address_find_one',
            ['customerId' => $customerId, 'vendorId' => $vendorId, 'id' => $address->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($this->getSerializer()->normalize($address), Response::HTTP_CREATED, [
            'Location' => $url
        ]);
    }

    /**
     * @return CustomerService
     */
    private function getCustomerService()
    {
        return $this->get('volo_frontend.service.customer');
    }
}
