<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends BaseController
{
    /**
     * @Route("/admin/cms/clear-cache", name="admin_cms_clear_cache")
     * @Method({"GET"})
     *
     * @param Request $request
     * @return Response
     * @throws NotFoundHttpException
     */
    public function clearCacheAction(Request $request)
    {
        if($request->get('password') !== $this->container->getParameter('cms_clear_cache_password')) {
            throw $this->createNotFoundException();
        }

        $this->get('volo_frontend.service.cms')->clearCmsCache();

        return new Response('cms cache cleared');
    }
}
