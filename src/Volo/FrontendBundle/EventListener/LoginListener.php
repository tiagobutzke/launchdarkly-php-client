<?php

namespace Volo\FrontendBundle\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Volo\FrontendBundle\Security\Token;
use Volo\FrontendBundle\Service\CustomerService;

class LoginListener
{
    /**
     * @var CustomerService
     */
    private $customerService;

    /**
     * @param CustomerService $customerService
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        /** @var Token $token */
        $token = $event->getAuthenticationToken();
        $accessToken = $token->getAccessToken();

        $this->customerService->saveUserAddressFromSession($session, $accessToken);
    }
}
