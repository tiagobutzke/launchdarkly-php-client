<?php

namespace Foodpanda\ApiSdk\Entity\Vendor;

use DateTime;
use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\Event\EventsCollection;

class MetaData extends DataObject
{
    /**
     * @var bool
     */
    protected $is_delivery_available;

    /**
     * @var bool
     */
    protected $is_pickup_available;

    /**
     * @var bool
     */
    protected $has_discount;

    /**
     * @var DateTime
     */
    protected $available_in;

    /**
     * @var string
     */
    protected $opening_times;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var EventsCollection
     */
    protected $events;

    public function __construct()
    {
        $this->events = new EventsCollection();
    }

    /**
     * @return boolean
     */
    public function isIsDeliveryAvailable()
    {
        return $this->is_delivery_available;
    }

    /**
     * @param boolean $is_delivery_available
     */
    public function setIsDeliveryAvailable($is_delivery_available)
    {
        $this->is_delivery_available = $is_delivery_available;
    }

    /**
     * @return boolean
     */
    public function isIsPickupAvailable()
    {
        return $this->is_pickup_available;
    }

    /**
     * @param boolean $is_pickup_available
     */
    public function setIsPickupAvailable($is_pickup_available)
    {
        $this->is_pickup_available = $is_pickup_available;
    }

    /**
     * @return boolean
     */
    public function isHasDiscount()
    {
        return $this->has_discount;
    }

    /**
     * @param boolean $has_discount
     */
    public function setHasDiscount($has_discount)
    {
        $this->has_discount = $has_discount;
    }

    /**
     * @return DateTime
     */
    public function getAvailableIn()
    {
        return $this->available_in;
    }

    /**
     * @param DateTime $available_in
     */
    public function setAvailableIn($available_in)
    {
        $this->available_in = $available_in;
    }

    /**
     * @return string
     */
    public function getOpeningTimes()
    {
        return $this->opening_times;
    }

    /**
     * @param string $opening_times
     */
    public function setOpeningTimes($opening_times)
    {
        $this->opening_times = $opening_times;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return EventsCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param EventsCollection $events
     */
    public function setEvents($events)
    {
        $this->events = $events;
    }
}
