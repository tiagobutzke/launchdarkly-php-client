<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Customer\CustomerPassword;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    const FLASH_TYPE_ERRORS = 'errors';

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

        $errorMessages = $this->get('session')->getFlashBag()->get(static::FLASH_TYPE_ERRORS);
        $serializer       = $this->get('volo_frontend.api.serializer');
        $customerProvider = $this->get('volo_frontend.provider.customer');
        $phoneNumberError = '';
        if ($request->getMethod() === Request::METHOD_POST) {
            try {
                $this->get('volo_frontend.service.customer')->updateCustomer($request->request->get('customer'));
            } catch (PhoneNumberValidationException $e) {
                $phoneNumberError = $e->getMessage();
            }
        }

        /** @var Token $token */
        $token       = $this->get('security.token_storage')->getToken();
        $accessToken = $token->getAccessToken();
        $customer    = $customerProvider->getCustomer($accessToken);
        $addresses   = $customerProvider->getAddresses($accessToken);
        $creditCards = $customerProvider->getAdyenCards($accessToken);

        return [
            'phoneNumberError'   => $phoneNumberError,
            'errorMessages'      => $errorMessages,
            'customer'           => $serializer->normalize($customer),
            'customer_addresses' => $serializer->normalize($addresses->getItems()),
            'customer_cards'     => $creditCards['items'],
        ];
    }

    /**
     * @Route("/update_password", name="profile_update_password")
     * @Method({"POST"})
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function updatePasswordAction(Request $request)
    {
        if (!$this->isGranted('ROLE_CUSTOMER')) {
            throw new AccessDeniedHttpException('Access denied for profile page.');
        }

        $customerPassword = $this->get('volo_frontend.api.serializer')->denormalize(
            $request->request->get('password_form'),
            CustomerPassword::class
        );

        try {
            $this->get('volo_frontend.service.customer')->updateCustomerPassword($customerPassword);
        } catch (ApiErrorException $e) {
            $this->addFlash(static::FLASH_TYPE_ERRORS, $e->getMessage());
        }

        return $this->redirectToRoute('profile_index');
    }
}
