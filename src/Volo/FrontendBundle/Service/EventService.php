<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Foodpanda\ApiSdk\Entity\Event\Event;
use Foodpanda\ApiSdk\Entity\Event\EventAction;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Provider\VendorProvider;

class EventService
{
    const ACTION_TYPE_MESSAGE = 'message';

    /**
     * @var VendorProvider
     */
    private $vendorProvider;

    /**
     * @param VendorProvider $vendorProvider
     */
    public function __construct(VendorProvider $vendorProvider)
    {
        $this->vendorProvider = $vendorProvider;
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

        $vendorItems = $this->vendorProvider->findVendorsMetaData(new GpsLocation($latitude, $longitude))->getItems();

        /** @var Vendor $vendor */
        foreach ($vendorItems as $vendor) {
            $events = $vendor->getMetadata()->getEvents();
            /** @var Event $event */
            foreach ($events as $event) {
                $messages = $event->getActions()->filter(function(EventAction $action) {
                    return $action->getType() === static::ACTION_TYPE_MESSAGE;
                })->map(function(EventAction $action) {
                    return $action->getMessage();
                });

                $eventMessages = array_merge($eventMessages, $messages->toArray());
            }
        }

        return array_unique($eventMessages);
    }
}
