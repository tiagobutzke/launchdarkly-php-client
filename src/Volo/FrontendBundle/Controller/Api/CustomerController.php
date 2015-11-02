<?php

namespace Volo\FrontendBundle\Controller\Api;

use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Foodpanda\ApiSdk\Exception\ValidationEntityException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Controller\BaseController;
use Volo\FrontendBundle\Service\CustomerService;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;

/**
 * @Route("/api/v1/customers", defaults={"_format": "json"}, condition="request.isXmlHttpRequest()")
 */
class CustomerController extends BaseController
{
    /**
     * @Route("/{id}", name="api_customers_update")
     * @Method({"PUT"})
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function updateContactInformationAction(Request $request, $id)
    {
        try {
            $data = $this->decodeJsonContent($request->getContent());

            if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $customer = $this->getCustomerService()->updateCustomer($data);
            } else {
                $request->getSession()->set(CustomerService::SESSION_CONTACT_KEY_TEMPLATE, $data);
                $customer = $this->getSerializer()->denormalizeAuthenticatedCustomer($data);
            }
        } catch (ApiErrorException $e) {
            $data = json_decode($e->getJsonErrorMessage(), true);
            if (isset($data['data']['exception_type'])
                && 'ApiObjectDoesNotExistException' === $data['data']['exception_type']) {

                throw $this->createNotFoundException(sprintf('Address not found: "%s"', $id));
            }

            return $this->get('volo_frontend.service.api_error_translator')->createTranslatedJsonResponse($e);
        } catch (PhoneNumberValidationException $e) {
            return new JsonResponse([
                'error' => [
                    'errors' => [
                        [
                            'field_name' => 'mobile_number',
                            'violation_messages' => [$e->getMessage()]
                        ]
                    ]
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $customer->setPassword('');
        $customer->setToken('');

        return new JsonResponse($this->getSerializer()->normalize($customer));
    }

    /**
     * @Route("/{id}", name="api_customers_get", options={"expose"=true})
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getCustomerAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse([], Response::HTTP_NOT_IMPLEMENTED);
        }

        $customer = $request->getSession()->get(CustomerService::SESSION_CONTACT_KEY_TEMPLATE);

        return new JsonResponse($this->getSerializer()->normalize($customer));
    }

    /**
     * @Route(
     *      "/validate/mobile-number/{phoneNumber}",
     *      name="customer_validate_phone_number",
     *      options={"expose"=true}
     * )
     * @Method({"GET"})
     *
     * @param string $phoneNumber
     *
     * @return JsonResponse
     */
    public function validatePhoneNumberAction($phoneNumber)
    {
        try {
            $validPhoneNumber = $this->getCustomerService()->validatePhoneNumber(urldecode($phoneNumber));
        } catch (PhoneNumberValidationException $e) {
            return new JsonResponse(['error' => ['mobile_number' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'mobile_country_code' => $validPhoneNumber->getCountryCode(),
            'mobile_number' => $validPhoneNumber->getNationalNumber(),
        ]);
    }

    /**
     * @Route(
     *      "/validate/customer-email/{email}",
     *      name="customer_validate_email",
     *      options={"expose"=true}
     * )
     * @Method({"GET"})
     *
     * @param string $email
     *
     * @return JsonResponse
     */
    public function validateEmailAction($email)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createNotFoundException('User is logged in.');
        }

        $isExistingUser = false;
        try {
            $this->getCustomerService()->createGuestCustomer(
                ['email' => urldecode($email)],
                []
            );
        } catch (ValidationEntityException $e) {
            $errors = $e->getValidationMessages();

            if (isset($errors['email'])) {
                return new JsonResponse(
                    [
                        'exists' => false,
                        'error' => [
                            'errors' => [
                                ['field_name' => 'email', 'violation_messages' => [$errors['email']]]
                            ]
                        ]
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
        } catch (ApiErrorException $e) {
            $error = json_decode($e->getJsonErrorMessage(), true);
            if (isset($error['data']['exception_type']) &&
                'ApiCustomerAlreadyExistsException' === $error['data']['exception_type']
            ) {
                $isExistingUser = true;
            }
        }

        return new JsonResponse([
            'exists' => $isExistingUser
        ]);
    }
}
