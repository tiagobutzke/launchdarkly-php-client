<?php

namespace Foodpanda\ApiSdk\Entity\Address;

use Foodpanda\ApiSdk\Entity\DataObject;

class Address extends DataObject
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $city_id;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var int
     */
    protected $area_id;

    /**
     * @var string
     */
    protected $areas;

    /**
     * @var string
     */
    protected $address_line1;

    /**
     * @var string
     */
    protected $address_line2;

    /**
     * @var string
     */
    protected $address_line3;

    /**
     * @var string
     */
    protected $address_line4;

    /**
     * @var string
     */
    protected $address_line5;

    /**
     * @var string
     */
    protected $address_other;

    /**
     * @var string
     */
    protected $room;

    /**
     * @var string
     */
    protected $flat_number;

    /**
     * @var string
     */
    protected $structure;

    /**
     * @var string
     */
    protected $building;

    /**
     * @var string
     */
    protected $intercom;

    /**
     * @var string
     */
    protected $entrance;

    /**
     * @var string
     */
    protected $floor;

    /**
     * @var string
     */
    protected $district;

    /**
     * @var string
     */
    protected $postcode;

    /**
     * @var string
     */
    protected $company;

    /**
     * @var double
     */
    protected $latitude;

    /**
     * @var double
     */
    protected $longitude;

    /**
     * @var bool
     */
    protected $is_delivery_available;

    /**
     * @var string
     */
    protected $formatted_customer_address;

    /**
     * @var string
     */
    protected $delivery_instructions;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getCityId()
    {
        return $this->city_id;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return int
     */
    public function getAreaId()
    {
        return $this->area_id;
    }

    /**
     * @return string
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->address_line1;
    }

    /**
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->address_line2;
    }

    /**
     * @return string
     */
    public function getAddressLine3()
    {
        return $this->address_line3;
    }

    /**
     * @return string
     */
    public function getAddressLine4()
    {
        return $this->address_line4;
    }

    /**
     * @return string
     */
    public function getAddressLine5()
    {
        return $this->address_line5;
    }

    /**
     * @return string
     */
    public function getAddressOther()
    {
        return $this->address_other;
    }

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @return string
     */
    public function getFlatNumber()
    {
        return $this->flat_number;
    }

    /**
     * @return string
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @return string
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @return string
     */
    public function getIntercom()
    {
        return $this->intercom;
    }

    /**
     * @return string
     */
    public function getEntrance()
    {
        return $this->entrance;
    }

    /**
     * @return string
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return boolean
     */
    public function isIsDeliveryAvailable()
    {
        return $this->is_delivery_available;
    }

    /**
     * @return string
     */
    public function getFormattedCustomerAddress()
    {
        return $this->formatted_customer_address;
    }

    /**
     * @return string
     */
    public function getDeliveryInstructions()
    {
        return $this->delivery_instructions;
    }
}
