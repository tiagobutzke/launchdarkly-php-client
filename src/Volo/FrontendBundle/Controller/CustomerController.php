<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Exception\ValidationEntityException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends Controller
{
    /**
     * @Route("/customer", name="customer.create")
     * @Template()
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param array $customer
     *
     * @return array
     * @internal param Request $request
     */
    public function createAction(Request $request, array $customer = [])
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $this->handleCustomerCreation($request);
            $customer = $request->request->get('customer');
        }

        return [
            'customer' => $customer,
        ];
    }

    /**
     * @param Request $request
     */
    public function handleCustomerCreation(Request $request)
    {
        $customerService = $this->get('volo_frontend.service.customer');

        try {
            $newCustomer = $customerService->createCustomer($request->request->get('customer'));

            // @TODO: Do something here, redirect or auto-login the user or anything similar (not sure about it yet)
            return dump($newCustomer);
        } catch (ValidationEntityException $e) {
            $errors = json_decode($e->getMessage(), true)['data']['items'];
            $this->createErrors($errors);
        } catch (\Exception $e) {
            // @TODO: ask PMs about the appropriate message and use the translation
            $this->addFlash('error', 'An error occurred, please try again');
        }
    }

    /**
     * @param array $errors
     */
    protected function createErrors(array $errors)
    {
        foreach ($errors as $error) {
            foreach ($error['violation_messages'] as $violationMessage) {
                $this->addFlash(
                    'error',
                    sprintf('%s: %s', $error['field_name'], $violationMessage)
                );
            }
        }
    }
}
