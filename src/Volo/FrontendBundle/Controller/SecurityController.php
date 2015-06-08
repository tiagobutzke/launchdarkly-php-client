<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();

        $view = $this->renderView('VoloFrontendBundle:Security:login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $error,
        ]);

        $statusCode = $error ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

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
