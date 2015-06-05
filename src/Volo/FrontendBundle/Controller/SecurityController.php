<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $statusCode = Response::HTTP_OK;
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        $view = $this->renderView('VoloFrontendBundle:Security:login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $error,
        ]);

        return new Response($view, $statusCode);
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
        // this controller will not be executed, as the route is handled by the Security system
    }
}
