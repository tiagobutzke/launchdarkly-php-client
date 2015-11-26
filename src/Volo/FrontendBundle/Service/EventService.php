<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Foodpanda\ApiSdk\Entity\Event\Event;
use Foodpanda\ApiSdk\Entity\Event\EventAction;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Volo\FrontendBundle\Service;

class EventService
{
    const ACTION_TYPE_MESSAGE = 'message';
    const CLOSE_REASON_EVENT = 'event';

    /**
     * @var VendorService
     */
    private $vendorService;

    /**
     * @param VendorService $vendorService
     */
    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return array
     */
    public function getActionMessages($latitude, $longitude) {
        $eventMessages = [];

        if ($latitude === null || $longitude === null) {
            return $eventMessages;
        }

        $vendorItems = $this->vendorService->findAll(new GpsLocation($latitude, $longitude));

        /** @var Vendor $vendor */
        foreach ($vendorItems as $vendor) {
            $event = $vendor->getMetadata()->getEvents();
            /** @var Event $event */
            $messages = $event->getActions()->filter(function(EventAction $action) {
                return $action->getType() === static::ACTION_TYPE_MESSAGE;
            })->map(function(EventAction $action) {
                return $action->getMessage();
            });

            $eventMessages = array_merge($eventMessages, $messages->toArray());
        }

        return array_unique($eventMessages);
    }

    /**
     * @param Vendor $vendor
     *
     * @return bool
     */
    public function isVendorFloodFeatureClosed(Vendor $vendor)
    {
        return in_array(static::CLOSE_REASON_EVENT, $vendor->getMetadata()->getCloseReasons(), true);
    }
}
