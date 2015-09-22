<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CmsController extends BaseController
{
    /**
     * @Route("/contents/{code}", name="cms")
     * @Template()
     */
    public function indexAction($code)
    {
        try {
            $element = $this->get('volo_frontend.provider.cms')->findByCode($code, false);
        } catch (EntityNotFoundException $exception) {
            throw $this->createNotFoundException('CMS item not found!', $exception);
        }

        if ($element->getCode() !== $code) {
            return $this->redirectToRoute('cms', ['code' => $element->getCode()]);
        }

        return [
            'element' => $element
        ];
    }
}
