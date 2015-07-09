<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Exception\ChangePasswordCustomerException;
use Foodpanda\ApiSdk\Exception\ValidationEntityException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Volo\FrontendBundle\Security\Token;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;

/**
 * @Route("/profile")
 * @Template()
 */
class ProfileController extends Controller
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

        $serializer       = $this->get('volo_frontend.api.serializer');
        $customerProvider = $this->get('volo_frontend.provider.customer');
        $errorMessage     = '';
        $phoneNumberError = '';
        $isChangePasswordSuccess = false;
        $passwordFormErrorMessages = [];
        if ($request->getMethod() === Request::METHOD_POST) {
            if ($request->request->has('customer')) {
                try {
                    $this->get('volo_frontend.service.customer')->updateCustomer($request->request->get('customer', []));
                } catch (PhoneNumberValidationException $e) {
                    $phoneNumberError = $e->getMessage();
                }
            }
            if ($request->request->has('password_form')) {
                try {
                    $this->updatePassword($request);
                    $isChangePasswordSuccess = true;
                } catch (ChangePasswordCustomerException $e) {
                    $errorMessage = $e->getValidationMessage();
                } catch (ValidationEntityException $e) {
                    $passwordFormErrorMessages = $e->getValidationMessages();
                }
            }
        }

        /** @var Token $token */
        $token       = $this->get('security.token_storage')->getToken();
        $accessToken = $token->getAccessToken();
        $customer    = $customerProvider->getCustomer($accessToken);
        $addresses   = $customerProvider->getAddresses($accessToken);
        $creditCards = $customerProvider->getAdyenCards($accessToken);

        return [
            'isChangePasswordSuccess'   => $isChangePasswordSuccess,
            'passwordFormErrorMessages' => $passwordFormErrorMessages,
            'phoneNumberError'   => $phoneNumberError,
            'errorMessage'       => $errorMessage,
            'customer'           => $serializer->normalize($customer),
            'customer_addresses' => $serializer->normalize($addresses->getItems()),
            'customer_cards'     => $creditCards['items'],
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
