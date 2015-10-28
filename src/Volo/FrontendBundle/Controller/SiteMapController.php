<?php

namespace Volo\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Exception\SiteMapNotFoundException;

class SiteMapController extends BaseController
{
    /**
     * @Route("/sitemap.xml")
     *
     * @return Response
     */
    public function indexAction()
    {
        try {
            $siteMap = $this->get('volo_frontend.service.site_map')->getSiteMap();
        } catch (SiteMapNotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage(), $e);
        }

        $response = new Response($siteMap);
        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }
}
