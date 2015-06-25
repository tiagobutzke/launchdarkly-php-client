<?php

namespace Volo\FrontendBundle\Twig;

use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Foodpanda\ApiSdk\Provider\CmsProvider;

class CmsExtension extends \Twig_Extension
{
    /**
     * @var CmsProvider
     */
    protected $cmsApiProvider;

    /**
     * @param CmsProvider $cmsApiProvider
     */
    public function __construct(CmsProvider $cmsApiProvider)
    {
        $this->cmsApiProvider = $cmsApiProvider;
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
            $content =  $this->cmsApiProvider->findByCode($code)->getContent();
        } catch (EntityNotFoundException $exception) {
            $content =  '';
        }

        if ('' === $content && null !== $fallback) {
            try {
                $content =  $this->cmsApiProvider->findByCode($fallback)->getContent();
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
