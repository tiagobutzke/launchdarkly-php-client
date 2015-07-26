<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Address\Address;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Foodpanda\ApiSdk\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Volo\FrontendBundle\Security\Token;
use Volo\FrontendBundle\Service\CustomerService;

/**
 * @Route(defaults={"_format": "json"})
 */
class CustomerAddressController extends BaseController
{
    /**
     * @Route("/customer/{customerId}/address", name="customer_address_list", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        $vendorId = $request->query->get('vendorId');

        $serializer = $this->getSerializer();
        $accessToken = $this->getToken()->getAccessToken();
        $addresses = $this->getCustomerService()->getAddresses($accessToken, $vendorId);

        return new JsonResponse($serializer->normalize($addresses)['items']);
    }

    /**
     * @Route("/customer/{customerId}/address/{id}", name="customer_address_find_one", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"GET"})
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function findOneAction(Request $request, $id)
    {
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
     * @Route("/customer/{customerId}/address/{id}", name="customer_address_update", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"PUT"})
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, $id)
    {
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
     * @Route("/customer/{customerId}/address/{id}", name="customer_address_delete", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"DELETE"})
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $accessToken = $this->getToken()->getAccessToken();

        try {
            $this->getCustomerService()->deleteAddress($id, $accessToken);
        } catch (ApiErrorException $e) {
            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        }

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/customer/{customerId}/address", name="customer_address_create", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        $vendorId    = $request->request->get('vendor_id');
        $serializer = $this->getSerializer();
        $token = $this->getToken();
        $accessToken = $token->getAccessToken();

        $data = $this->sanitizeInputData($this->decodeJsonContent($request));

        /** @var Address $address */
        $address = $serializer->denormalize($data, Address::class);
        try {
            $address = $this->getCustomerService()->findAddressOrCreate($address, $accessToken);
        } catch (ApiErrorException $e) {
            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        }

        return new RedirectResponse(
            $this->generateUrl(
                'customer_address_find_one',
                ['customerId' => 'me', 'vendorId' => $vendorId, 'id' => $address->getId()]
            )
        );
    }

    /**
     * @return Serializer
     */
    private function getSerializer()
    {
        return $this->get('volo_frontend.api.serializer');
    }

    /**
     * @return CustomerService
     */
    private function getCustomerService()
    {
        return $this->get('volo_frontend.service.customer');
    }

    /**
     * @return Token
     */
    private function getToken()
    {
        return $this->get('security.token_storage')->getToken();
    }
}
