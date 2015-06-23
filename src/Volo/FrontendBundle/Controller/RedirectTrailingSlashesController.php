<?php

namespace Volo\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectTrailingSlashesController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/{url}", requirements={"url"=".*\/"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function redirectTrailingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        return $this->redirect($url, Response::HTTP_MOVED_PERMANENTLY);
    }
}
