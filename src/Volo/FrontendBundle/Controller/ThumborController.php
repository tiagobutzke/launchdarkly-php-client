<?php

namespace Volo\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ThumborController extends BaseController
{
    /**
     * @Route("/thumbor/configuration.js", name="thumbor_configuration")
     */
    public function configurationAction()
    {
        $content = $this->container->get('volo_frontend.thumbor_dumper')->dump();

        $maxAge = 604800;
        $response = new Response($content, Response::HTTP_OK, array('Content-Type' => 'application/javascript'));
        $response->setPublic();
        $response->setMaxAge($maxAge);
        $response->setSharedMaxAge($maxAge);

        return $response;
    }

    /**
     * @Route("/thumbor/fake/{hash}", name="thumbor_fake", requirements={"hash"=".+"})
     */
    public function fakeAction()
    {
        if (!$this->container->getParameter('kernel.debug')) {
            throw $this->createNotFoundException();
        }

        $data = file_get_contents($this->get('kernel')->getRootDir() . '/../web/img/hero_banners/hero_banner_menu_4000.jpg');

        $response = new Response($data, Response::HTTP_OK, ['Content-Type' => 'image/jpeg']);
        $response->setPublic();
        $response->setMaxAge(86400);

        return $response;
    }
}
