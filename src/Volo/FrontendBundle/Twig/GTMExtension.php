<?php

namespace Volo\FrontendBundle\Twig;

use Foodpanda\ApiSdk\Entity\Cuisine\Cuisine;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Extensions_Extension_Intl;
use Volo\FrontendBundle\Service\ScheduleService;

class GTMExtension extends Twig_Extensions_Extension_Intl
{
    const REFERRAL_KEYWORD_NAME = 'referralKeyword';
    const REFERRAL_KEYWORD_KEY = 'q';

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var ScheduleService
     */
    protected $scheduleService;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param string $countryCode
     * @param ScheduleService $scheduleService
     * @param RequestStack $requestStack
     */
    public function __construct($countryCode, ScheduleService $scheduleService, RequestStack $requestStack)
    {
        parent::__construct();

        $this->countryCode = $countryCode;
        $this->scheduleService = $scheduleService;
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
            new \Twig_SimpleFunction('gtm_extract_vendor_fields', array($this, 'getVendorFields')),
            new \Twig_SimpleFunction('gtm_is_vendor_open_now', array($this, 'getIsVendorOpenNow')),
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

    /**
     * @param Vendor $vendor
     *
     * @return array
     */
    public function getVendorFields(Vendor $vendor)
    {
        $cuisines = [];
        /** @var Cuisine $cuisine */
        foreach ($vendor->getCuisines() as $cuisine) {
            $cuisines[] = $cuisine->getName();
        }

        return [
            'id' => $vendor->getId(),
            'name' => $vendor->getName(),
            'code' => $vendor->getCode(),
            'category' => implode(',', $cuisines)
        ];
    }

    /**
     * @param Vendor $vendor
     *
     * @return string
     */
    public function getIsVendorOpenNow(Vendor $vendor)
    {
        return $this->scheduleService->isVendorOpen($vendor, new \DateTime())
            ? 'open'
            : 'closed'
            ;
    }
}
