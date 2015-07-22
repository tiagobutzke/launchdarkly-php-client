<?php

namespace Volo\FrontendBundle\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Extensions_Extension_Intl;

class GTMExtension extends Twig_Extensions_Extension_Intl
{
    const REFERRAL_KEYWORD_NAME = 'referralKeyword';
    const REFERRAL_KEYWORD_KEY = 'q';

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
            new \Twig_SimpleFunction('gtm_get_referral_keyword', array($this, 'getReferralKeyword')),
        ];
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return \Locale::getDisplayRegion('-' . $this->countryCode, 'en');
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        $headers = $this->request->headers;

        return $headers->get('referer');
    }

    /**
     * @return string
     */
    public function getReferralKeyword()
    {
        $session = $this->request->getSession();

        $word = $session->get(static::REFERRAL_KEYWORD_NAME, '');
        if ($word === '') {
            $word = $this->request->query->get(static::REFERRAL_KEYWORD_KEY, '');
            $session->set(static::REFERRAL_KEYWORD_NAME, $word);
        }

        return $word;
    }
}
