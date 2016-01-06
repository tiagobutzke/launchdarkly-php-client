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
     *
     * @param $code
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction($code)
    {
        try {
            $element = $this->get('volo_frontend.service.cms')->findByCode($code);
        } catch (EntityNotFoundException $exception) {
            throw $this->createNotFoundException('CMS item not found!', $exception);
        }

        if ($element->getCode() !== $code && strtolower($element->getCode()) === strtolower($code)) {
            return $this->redirectToRoute('cms', ['code' => strtolower($element->getCode())]);
        }

        return [
            'element' => $element
        ];
    }
}
