<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CmsController extends Controller
{
    /**
     * @Route("/contents/{code}", name="cms")
     * @Route("/privacy", name="privacy", defaults={"code": "privacy.htm"})
     * @Template()
     */
    public function indexAction($code)
    {
        try {
            $element = $this->get('volo_frontend.provider.cms')->findByCode($code);
        } catch (EntityNotFoundException $exception) {
            throw $this->createNotFoundException('CMS item not found!', $exception);
        }

        return [
            'element' => $element
        ];
    }
}
