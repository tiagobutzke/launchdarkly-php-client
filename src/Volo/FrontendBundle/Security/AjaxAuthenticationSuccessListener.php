<?php

namespace Volo\FrontendBundle\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Volo\FrontendBundle\Service\CustomerService;

class AjaxAuthenticationSuccessListener extends DefaultAuthenticationSuccessHandler
{
    /**
     * @var CustomerService
     */
    private $customerService;

    /**
     * Constructor.
     *
     * @param HttpUtils $httpUtils
     * @param array $options Options for processing a successful authentication attempt.
     * @param CustomerService $customerService
     */
    public function __construct(HttpUtils $httpUtils, array $options = [], CustomerService $customerService = null)
    {
        parent::__construct($httpUtils, $options);

        $this->customerService = $customerService;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($request->isXmlHttpRequest()) {
            if ($request->headers->has('FD-save-address')) {
                $address = json_decode($request->headers->get('FD-save-address'), true);

                $this->customerService->saveUserAddressFromSession($address, $token->getAccessToken());
            }
            $response = new JsonResponse(['url' => $this->determineTargetUrl($request)]);
        } else {
            $response = parent::onAuthenticationSuccess($request, $token);
        }

        return $response;
    }
}
