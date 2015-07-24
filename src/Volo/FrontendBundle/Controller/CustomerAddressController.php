<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Address\Address;
use Foodpanda\ApiSdk\Provider\CustomerProvider;
use Foodpanda\ApiSdk\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Volo\FrontendBundle\Security\Token;
use Volo\FrontendBundle\Service\CustomerService;

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
        $address = $this->getCustomerService()->getAddress($id, $accessToken, $vendorId);

        return new JsonResponse($serializer->normalize($address));
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
        $address = $this->getCustomerService()->findAddressOrCreate($address, $accessToken);

        return new RedirectResponse($this->generateUrl('customer_address_find_one', ['customerId' => 0, 'vendorId' => $vendorId, 'id' => $address->getId()]));
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
