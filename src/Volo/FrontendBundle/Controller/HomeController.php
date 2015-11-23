<?php

namespace Volo\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Volo\FrontendBundle\Service\CustomerLocationService;

class HomeController extends BaseController
{
    /**
     * @Route("/", name="home", defaults={"postalCode": ""}, options={"expose"=true})
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
        $isFullAddressAutocomplete = $this->getAddressConfigProvider()->isFullAddressAutocomplete();
        if ('' === $postalCode && false === $isFullAddressAutocomplete) {
            $postalCode = $location[CustomerLocationService::KEY_PLZ];
        }

        return [
            'postalCode' => $postalCode,
            'showVendorPopup' => in_array($utmSource, ['heimschmecker.at', 'urbantaste.de']) ? true : false,
            'showUnsubscribePopup' => $request->query->has('showUnsubscribePopup'),
            'isUnsubscribed' => $request->query->get('isUnsubscribed'),
            'utmSource' => $utmSource,
            'isFullAddressAutocomplete' => $isFullAddressAutocomplete,
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
}
