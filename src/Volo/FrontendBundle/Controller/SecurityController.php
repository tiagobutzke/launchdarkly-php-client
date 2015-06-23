<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $username = $request->get('username', $authenticationUtils->getLastUsername());
        $errorMessage = $request->get('error', null);
        $statusCode = Response::HTTP_OK;

        if (null !== $errorMessage) {
            $error = ['messageKey' => $errorMessage, 'messageData' => []];
        } else {
            $error = $authenticationUtils->getLastAuthenticationError();
            if ($error !== null) {
                $statusCode = Response::HTTP_BAD_REQUEST;
            }
        }

        $view = $this->renderView('VoloFrontendBundle:Security:login.html.twig', [
            'last_username' => $username,
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
