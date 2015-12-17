<?php

namespace Volo\FrontendBundle\Twig;

use Foodpanda\ApiSdk\Entity\Cms\CmsItem;
use Foodpanda\ApiSdk\Entity\Cuisine\Cuisine;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Volo\FrontendBundle\Service\CmsService;

class CmsExtension extends \Twig_Extension
{
    /**
     * @var CmsService
     */
    protected $cmsService;

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @param CmsService $cmsService
     * @param string $countryCode
     */
    public function __construct(CmsService $cmsService, $countryCode)
    {
        $this->cmsService = $cmsService;
        $this->countryCode = $countryCode;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cms', [$this, 'getCmsContent'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_keywords', [$this, 'getCmsKeywords'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_title', [$this, 'getCmsTitle']),
            new \Twig_SimpleFunction('cms_description', [$this, 'getCmsDescription']),
            new \Twig_SimpleFunction('cms_robots', [$this, 'getCmsRobots']),
            new \Twig_SimpleFunction(
                'cms_string_replace_tokens',
                [$this, 'replaceTokens'],
                ['is_safe' => ['html']]
            ),
        );
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('cms_seo_replace_company', [$this, 'replaceCompanySeoToken']),
        ];
    }

    /**
     * @param string $code
     * @param string $fallback
     *
     * @return string
     */
    public function getCmsContent($code, $fallback = null)
    {
        return $this->getCms($code, $fallback, 'content');
    }

    /**
     * @param string $code
     * @param string $fallback
     *
     * @return string
     */
    public function getCmsTitle($code, $fallback = null)
    {
        return $this->getCms($code, $fallback, 'title');
    }

    /**
     * @param string $code
     * @param string $fallback
     *
     * @return string
     */
    public function getCmsKeywords($code, $fallback = null)
    {
        return $this->getCms($code, $fallback, 'keywords');
    }

    /**
     * @param string $code
     * @param string $fallback
     *
     * @return string
     */
    public function getCmsDescription($code, $fallback = null)
    {
        return $this->getCms($code, $fallback, 'description');
    }

    /**
     * @param string $code
     * @param string $fallback
     *
     * @return string
     */
    public function getCmsRobots($code, $fallback = null)
    {
        return $this->getCms($code, $fallback, 'robots');
    }

    /**
     * @param string $string
     * @param Vendor $vendor
     *
     * @return string
     */
    public function replaceTokens($string, Vendor $vendor)
    {
        $companyName = $this->findCompanySeoName();

        $cuisines = $vendor->getCuisines()->map(function(Cuisine $cuisine){
            return $cuisine->getName();
        });

        return str_replace(
            [
                '{city}',
                '{vendor}',
                '{company}',
                '{cuisine}',
            ],
            [
                $vendor->getCity()->getName(),
                $vendor->getName(),
                $companyName,
                implode(', ', $cuisines->toArray())
            ],
            $string
        );
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function replaceCompanySeoToken($string)
    {
        $companyName = $this->findCompanySeoName();

        return str_replace('{company}', $companyName, $string);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cms_extension';
    }

    /**
     * @param string $code
     * @param string $fallback
     * @param string $defaultField
     *
     * @return string
     */
    protected function getCms($code, $fallback = null, $defaultField)
    {
        try {
            $value =  $this->getCmsField($this->cmsService->findByCode($code), $defaultField);
        } catch (EntityNotFoundException $exception) {
            $value =  '';
        }

        if ('' === $value && null !== $fallback) {
            try {
                $value =  $this->getCmsField($this->cmsService->findByCode($fallback), $defaultField);
            } catch (EntityNotFoundException $exception) {
                $value =  '';
            }
        }

        return $value;
    }

    /**
     * @param CmsItem $cmsItem
     * @param string $field
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function getCmsField(CmsItem $cmsItem, $field)
    {
        $fieldMethod = [
            'content' => 'getContent', /** @see CmsItem::getContent */
            'title' => 'getMetaTitle', /** @see CmsItem::getMetaTitle */
            'keywords' => 'getMetaKeywords', /** @see CmsItem::getMetaKeywords */
            'description' => 'getMetaDescription', /** @see CmsItem::getMetaDescription */
            'robots' => 'getMetaRobots', /** @see CmsItem::getMetaRobots */
        ];

        if (!array_key_exists($field, $fieldMethod)) {
            throw new \RuntimeException("The field `$fieldMethod` is not found in CmsItem class");
        }

        return $cmsItem->{$fieldMethod[$field]}();
    }

    /**
     * @return string
     */
    protected function findCompanySeoName()
    {
        $companyName = 'foodora';
        $countryCodeCompany = [
            'au' => 'suppertime',
            'ca' => 'hurrier'
        ];

        if (array_key_exists($this->countryCode, $countryCodeCompany)) {
            $companyName = $countryCodeCompany[$this->countryCode];
        }

        return $companyName;
    }
}
