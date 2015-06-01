<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Api\Auth\Credentials;
use Foodpanda\ApiSdk\Exception\ValidationEntityException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Volo\FrontendBundle\Exception\Location\MissingKeysException;
use Volo\FrontendBundle\Service\CustomerLocationService;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;

class CustomerController extends Controller
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

                return $this->redirectToRoute('home');
            } catch (PhoneNumberValidationException $e) {
                $errorMessages[] = $this->get('translator')->trans(sprintf('%s: %s', 'Phone number', $e->getMessage()));
                $statusCode = Response::HTTP_BAD_REQUEST;
            } catch (ValidationEntityException $e) {
                $errors = json_decode($e->getMessage(), true)['data']['items'];
                $errorMessages = $this->createErrors($errors);
                $statusCode = Response::HTTP_BAD_REQUEST;
            } catch (\Exception $e) {
                // @TODO: ask PMs about the appropriate message and use the translation
                $errorMessages[] = 'An error occurred, please try again';
                $statusCode = Response::HTTP_BAD_REQUEST;
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
     * @Method({"PUT"})
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

        $gpsLocation = $customerLocationService->create(
            $request->request->get(CustomerLocationService::KEY_LAT),
            $request->request->get(CustomerLocationService::KEY_LNG),
            $request->request->get(CustomerLocationService::KEY_PLZ),
            $request->request->get(CustomerLocationService::KEY_CITY),
            $request->request->get(CustomerLocationService::KEY_ADDRESS)
        );

        try {
            $customerLocationService->set($request->getSession()->getId(), $gpsLocation);
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
                $errorMessages[] = sprintf('%s: %s', $error['field_name'], $violationMessage);
            }
        }

        return $errorMessages;
    }
}
