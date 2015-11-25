<?php

namespace Volo\FrontendBundle\Twig;

use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Volo\FrontendBundle\Service\CmsService;

class CmsExtension extends \Twig_Extension
{
    /**
     * @var CmsService
     */
    protected $cmsService;

    /**
     * @param CmsService $cmsService
     */
    public function __construct(CmsService $cmsService)
    {
        $this->cmsService = $cmsService;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cms', [$this, 'getCmsContent'], ['is_safe' => ['html']]),
        );
    }

    /**
     * @param $code
     * @param null $fallback
     *
     * @return string
     */
    public function getCmsContent($code, $fallback = null)
    {
        try {
            $content =  $this->cmsService->findByCode($code)->getContent();
        } catch (EntityNotFoundException $exception) {
            $content =  '';
        }

        if ('' === $content && null !== $fallback) {
            try {
                $content =  $this->cmsService->findByCode($fallback)->getContent();
            } catch (EntityNotFoundException $exception) {
                $content =  '';
            }
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cms_extension';
    }
}
