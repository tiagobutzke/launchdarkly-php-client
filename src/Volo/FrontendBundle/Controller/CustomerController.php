<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Api\Auth\Credentials;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Foodpanda\ApiSdk\Exception\ChangePasswordCustomerException;
use Foodpanda\ApiSdk\Exception\ValidationEntityException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Volo\FrontendBundle\Exception\Location\MissingKeysException;
use Volo\FrontendBundle\Service\CustomerLocationService;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;

class CustomerController extends BaseController
{
    /**
     * @Route("/customer", name="customer.create", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $errorMessages = [];
        $customer = [];
        $statusCode = Response::HTTP_OK;

        if ($request->isMethod(Request::METHOD_POST)) {
            $customerService = $this->get('volo_frontend.service.customer');

            try {
                $customerData = $request->request->get('customer');
                $newCustomer = $customerService->createCustomer($customerData);

                $credentials = new Credentials($newCustomer->getEmail(), $customerData['password']);
                $token = $this->container->get('volo_frontend.security.authenticator')->login($credentials);

                $this->get('security.token_storage')->setToken($token);

                return new JsonResponse([
                    'url' => $this->generateUrl('home')
                ]);
            } catch (PhoneNumberValidationException $e) {
                $errorMessages[] = $e->getMessage();
                $statusCode = Response::HTTP_BAD_REQUEST;
            } catch (ValidationEntityException $e) {
                $errors = json_decode($e->getMessage(), true)['data']['items'];
                $errorMessages = $this->createErrors($errors);
                $statusCode = Response::HTTP_BAD_REQUEST;
            } catch (ApiErrorException $e) {
                $decodedError = json_decode($e->getJsonErrorMessage(), true);
                if ('ApiCustomerAlreadyExistsException' === $decodedError['data']['exception_type']) {
                    $errorMessages[] = $decodedError['data']['message'];
                    $statusCode = Response::HTTP_BAD_REQUEST;
                } else {
                    throw $e;
                }
            }

            $customer = $request->request->get('customer', []);
        }

        $view = $this->renderView('VoloFrontendBundle:Customer:create.html.twig', [
            'customer' => $customer,
            'errors'   => $errorMessages,
        ]);

        return new Response($view, $statusCode);
    }

    /**
     * @Route("/customer/forgot_password", name="customer.forgot_password", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return array
     */
    public function forgotPasswordAction(Request $request)
    {
        $email = '';
        $errorMessages = [];
        $statusCode = Response::HTTP_OK;

        if ($request->isMethod(Request::METHOD_POST)) {
            try {
                $data = $this->get('volo_frontend.provider.customer')->forgotPassword($request->request->get('_email'));

                if ($data) {
                    return new Response(
                        $this->renderView('VoloFrontendBundle:Customer:forgot_password_success.html.twig')
                    );
                }

                $errorMessages[] = $this->get('translator')->trans('error.forgot_password.email_wrong');
                $statusCode = Response::HTTP_BAD_REQUEST;

            } catch (ValidationEntityException $e) {
                $errors = json_decode($e->getMessage(), true)['data']['items'];
                $errorMessages = $this->createErrors($errors);
                $statusCode = Response::HTTP_BAD_REQUEST;
            } catch (ApiErrorException $e) {
                $errorMessages[] = $this->get('translator')->trans('error.forgot_password.email_wrong');
                $statusCode = Response::HTTP_BAD_REQUEST;
            }
        }

        $view = $this->renderView('VoloFrontendBundle:Customer:forgot_password.html.twig', [
            'email'  => $email,
            'errors' => $errorMessages,
        ]);

        return new Response($view, $statusCode);
    }


    /**
     * @Route("/reset-password/{code}", name="customer_reset_password", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param string  $code
     *
     * @return array
     */
    public function resetPasswordAction(Request $request, $code)
    {
        $errors = [];
        $httpCode = Response::HTTP_OK;

        if ($request->getMethod() === Request::METHOD_POST) {
            $password = $request->request->get('_password');

            try {
                $this->get('volo_frontend.provider.customer')->resetPassword($password, $code);
            } catch (ChangePasswordCustomerException $e) {
                $errors[] = $e->getExceptionType();
                $httpCode = Response::HTTP_BAD_REQUEST;
            } catch (ValidationEntityException $e) {
                $errors = $e->getValidationMessages()['new_password'];
                $httpCode = Response::HTTP_BAD_REQUEST;
            }
        }


        return new Response(
            $this->renderView(
                "VoloFrontendBundle:Customer:reset_password.html.twig",
                ['errors' => $errors, 'code' => $code]
            ),
            $httpCode
        );
    }

    /**
     * @Route(
     *      "/customer/location",
     *      name="volo_customer_set_location",
     *      options={"expose"=true},
     *      requirements={
     *          "latitude"="-?(\d*[.])?\d+",
     *          "longitude"="-?(\d*[.])?\d+",
     *          "postcode"="\d+"
     *      }
     * )
     * @Method({"POST", "PUT"})
     *
     * @param Request $request
     *
     * @throws BadRequestHttpException
     *
     * @return array
     */
    public function userLocationAction(Request $request)
    {
        $customerLocationService = $this->get('volo_frontend.service.customer_location');

        $data = $this->decodeJsonContent($request->getContent());
        if (!array_key_exists(CustomerLocationService::KEY_STREET, $data)) {
            $data[CustomerLocationService::KEY_STREET] = '';
        }
        if (!array_key_exists(CustomerLocationService::KEY_BUILDING, $data)) {
            $data[CustomerLocationService::KEY_BUILDING] = '';
        }
        $gpsLocation = $customerLocationService->create(
            $data[CustomerLocationService::KEY_LAT],
            $data[CustomerLocationService::KEY_LNG],
            $data[CustomerLocationService::KEY_PLZ],
            $data[CustomerLocationService::KEY_CITY],
            $data[CustomerLocationService::KEY_ADDRESS],
            $data[CustomerLocationService::KEY_STREET],
            $data[CustomerLocationService::KEY_BUILDING]
        );

        try {
            $customerLocationService->set($request->getSession(), $gpsLocation);
        } catch (MissingKeysException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return new JsonResponse();
    }

    protected function createErrors(array $errors)
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            foreach ($error['violation_messages'] as $violationMessage) {
                $errorMessages[] = $violationMessage;
            }
        }

        return $errorMessages;
    }
}
