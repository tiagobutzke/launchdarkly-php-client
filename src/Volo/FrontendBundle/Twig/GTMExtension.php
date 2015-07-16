<?php

namespace Volo\FrontendBundle\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Extensions_Extension_Intl;

class GTMExtension extends Twig_Extensions_Extension_Intl
{
    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param string $countryCode
     * @param RequestStack $requestStack
     */
    public function __construct($countryCode, RequestStack $requestStack)
    {
        parent::__construct();

        $this->countryCode = $countryCode;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('gtm_get_country', array($this, 'getCountry')),
            new \Twig_SimpleFunction('gtm_get_referrer', array($this, 'getReferrer')),
        ];
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return \Locale::getDisplayRegion('-' . $this->countryCode);
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        $headers = $this->request->headers;

        return $headers->get('referer');
    }
}
