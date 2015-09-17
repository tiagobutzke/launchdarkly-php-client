<?php

namespace Volo\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ComingSoonController extends BaseController
{
    /**
     * @Route("/testing-mode", name="_coming-soon-disable")
     *
     * @param Request $request
     *
     * @return array
     */
    public function homeAction(Request $request)
    {
        $response = new Response('Coming soon page disabled.');
        $response->headers->setCookie(new Cookie('coming-soon-disabled', 'true', 0, '/', null, $request->isSecure(), false));

        return $response;
    }

}
