<?php

namespace Volo\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ThumborController extends Controller
{
    /**
     * @Route("/thumbor/configuration.js", name="thumbor_configuration")
     */
    public function configurationAction()
    {
        $this->container->getParameter('phumbor.transformations');
        $content = [
            'breakpoints' => [],
        ];

        // @TODO provide just the thumbor config and generate the bLazy conf using JS
        foreach ($this->container->getParameter('phumbor.transformations') as $key => $transformer) {
            if (false === strrpos($key, '_retina')) {
                $content['breakpoints'][] = [
                    'width' => $transformer['resize']['width'],
                    'src' => 'data-src-' . $key,
                    'mode' => 'viewport',
                ];
            }
        }
        $content = sprintf('var thumbor = JSON.parse(\'%s\')', json_encode($content));

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

        $data = file_get_contents($this->get('kernel')->getRootDir() . '/../web/img/hero_banners/hero_banner_menu_600.jpg');

        $response = new Response($data, Response::HTTP_OK, ['Content-Type' => 'image/jpeg']);
        $response->setPublic();
        $response->setMaxAge(86400);

        return $response;
    }
}
