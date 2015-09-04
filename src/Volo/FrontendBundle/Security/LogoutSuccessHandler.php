<?php

namespace Volo\FrontendBundle\Security;

use JMS\I18nRoutingBundle\Router\I18nRouter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /**
     * @var I18nRouter
     */
    private $i18nRouter;

    /**
     * @param I18nRouter $i18nRouter
     */
    public function __construct(I18nRouter $i18nRouter)
    {
        $this->i18nRouter = $i18nRouter;
    }

    /**
     * @inheritdoc
     */
    public function onLogoutSuccess(Request $request)
    {
        return new RedirectResponse($this->i18nRouter->generate('home'));
    }
}
