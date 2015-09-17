<?php

namespace Volo\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Volo\FrontendBundle\Service\CustomerLocationService;

class HomeController extends BaseController
{
    /**
     * @Route("/", name="home", defaults={"postalCode": ""})
     * @Route("/filter/{postalCode}", name="filter_postalCode")
     * @Template()
     *
     * @param Request $request
     * @param string $postalCode
     *
     * @return array
     */
    public function homeAction(Request $request, $postalCode)
    {
        $utmSource = $request->query->get('utm_source');
        $location = $this->getCustomerLocationService()->get($request->getSession());
        if ('' === $postalCode && false === $this->getAddressConfigProvider()->isFullAddressAutocomplete()) {
            $postalCode = $location[CustomerLocationService::KEY_PLZ];
        }

        return [
            'postalCode' => $postalCode,
            'showVendorPopup' => $utmSource === 'heimschmecker.at' ? true : false
        ];
    }

    /**
     * @Route("/site/index/code/{code}", name="home_with_change_password_modal")
     * @Template()
     * @param string $code
     *
     * @return array
     */
    public function showResetPasswordModalAction($code)
    {
        return [
            'code' => $code,
        ];
    }

    /**
     * @return CustomerLocationService
     */
    private function getCustomerLocationService()
    {
        return $this->get('volo_frontend.service.customer_location');
    }

    /**
     * @return \Volo\FrontendBundle\Provider\AddressConfigProvider
     */
    private function getAddressConfigProvider()
    {
        return $this->get('volo_frontend.provider.address_config_provider');
    }
}
