<?php

namespace Volo\FrontendBundle\Twig;

use Twig_Extensions_Extension_Intl;
use Volo\FrontendBundle\Service\GTMService;

class GTMExtension extends Twig_Extensions_Extension_Intl
{
    /**
     * @var string
     */
    private $countryCode;

    /**
     * @param string $countryCode
     */
    public function __construct($countryCode)
    {
        parent::__construct();

        $this->countryCode = $countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('gtm_get_country', array($this, 'getCountry')),
        ];
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return \Locale::getDisplayRegion('-' . $this->countryCode);
    }
}
