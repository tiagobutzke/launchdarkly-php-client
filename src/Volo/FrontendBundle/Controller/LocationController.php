<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Volo\FrontendBundle\Service\CustomerLocationService;
use Foodpanda\ApiSdk\Entity\Vendor\VendorsCollection;

class LocationController extends Controller
{
    /**
     * @Route("/city/{id}", name="city")
     * @Template("VoloFrontendBundle:Location:vendors_list.html.twig")
     */
    public function cityAction($id)
    {
        $city    = $this->get('volo_frontend.provider.city')->find($id);
        $vendors = $this->get('volo_frontend.provider.vendor')->findVendorsByCity($city);

        list($openVendors, $closedVendorsWithPreorder) = $this->filterOpenClosedVendors($vendors->getItems());

        return [
            'location'      => $city,
            'openVendors'   => $openVendors,
            'closedVendors' => $closedVendorsWithPreorder
        ];
    }

    /**
     * @Route("/search", name="search_vendors")
     * @Template("VoloFrontendBundle:Location:vendors_list.html.twig")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return array
     */
    public function searchAction(Request $request)
    {
        $customerLocationService = $this->get('volo_frontend.service.customer_location');
        $vendorProvider = $this->get('volo_frontend.provider.vendor');

        $sessionId = $request->getSession()->getId();

        $location = [];
        $locationKeys = [
            CustomerLocationService::KEY_LAT,
            CustomerLocationService::KEY_LNG,
            CustomerLocationService::KEY_FORMATTED_ADDRESS,
        ];

        foreach ($locationKeys as $key) {
            $location[$key] = $request->get($key);
        }

        $customerLocationService->validate($location);
        $customerLocationService->set($sessionId, $location);

        $persistedLocation = $customerLocationService->get($sessionId);

        $vendors = $vendorProvider->findVendorsByLatLng(
            $persistedLocation[CustomerLocationService::KEY_LAT],
            $persistedLocation[CustomerLocationService::KEY_LNG]
        );
        list($openVendors, $closedVendorsWithPreorder) = $this->filterOpenClosedVendors($vendors->getItems());

        return [
            'openVendors' => $openVendors,
            'closedVendors' => $closedVendorsWithPreorder,
            'location' => [
                'name' => $persistedLocation[CustomerLocationService::KEY_FORMATTED_ADDRESS]
            ]
        ];
    }

    /**
     * @param VendorsCollection $items
     *
     * @return array
     */
    protected function filterOpenClosedVendors(VendorsCollection $items)
    {
        /** @var Vendor[] $openVendors */
        $openVendors = $items->filter(function (Vendor $vendor) {
            return $vendor->isIsPickupEnabled() || $vendor->isIsDeliveryEnabled();
        });

        /** @var Vendor[] $closedVendorsWithPreorder */
        $closedVendorsWithPreorder = $items->filter(function (Vendor $vendor) {
            return !$vendor->isIsPickupEnabled() && !$vendor->isIsDeliveryEnabled() && $vendor->isIsPreorderEnabled();
        });

        return [$openVendors, $closedVendorsWithPreorder];
    }
}
