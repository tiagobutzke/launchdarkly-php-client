<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Exception\ChangePasswordCustomerException;
use Foodpanda\ApiSdk\Exception\ValidationEntityException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/profile")
 * @Template()
 */
class ProfileController extends BaseController
{
    /**
     * @Route("", name="profile_index")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_CUSTOMER')) {
            throw new AccessDeniedHttpException('Access denied for profile page.');
        }

        $customerProvider = $this->get('volo_frontend.provider.customer');
        $errorMessage     = '';
        $isChangePasswordSuccess = false;
        $passwordFormErrorMessages = [];
        if ($request->getMethod() === Request::METHOD_POST) {
            if ($request->request->has('password_form')) {
                try {
                    $this->updatePassword($request);
                    $isChangePasswordSuccess = true;
                } catch (ChangePasswordCustomerException $e) {
                    $errorMessage = $e->getExceptionType();
                } catch (ValidationEntityException $e) {
                    $passwordFormErrorMessages = $e->getValidationMessages();
                }
            }
        }

        $accessToken = $this->getToken()->getAccessToken();
        $customer    = $customerProvider->getCustomer($accessToken);
        $addresses   = $customerProvider->getAddresses($accessToken);

        return [
            'isChangePasswordSuccess'   => $isChangePasswordSuccess,
            'passwordFormErrorMessages' => $passwordFormErrorMessages,
            'errorMessage'       => $errorMessage,
            'customer'           => $this->getSerializer()->normalize($customer),
            'customer_addresses' => $this->getSerializer()->normalize($addresses->getItems()),
            'customer_cards'     => $customerProvider->getAdyenCards($accessToken)['items'],
        ];
    }

    /**
     * @param Request $request
     */
    private function updatePassword(Request $request)
    {
        $data = $request->request->get('password_form', []);
        $customerPassword = $this->get('volo_frontend.api.serializer')->denormalizeCustomerPassword($data);
        $this->get('volo_frontend.service.customer')->updateCustomerPassword($customerPassword);
    }
}
