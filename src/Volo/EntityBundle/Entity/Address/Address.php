<?php

namespace Volo\EntityBundle\Entity\Address;

use Volo\EntityBundle\Entity\DataObject;

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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getCityId()
    {
        return $this->city_id;
    }

    /**
     * @param int $city_id
     */
    public function setCityId($city_id)
    {
        $this->city_id = $city_id;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return int
     */
    public function getAreaId()
    {
        return $this->area_id;
    }

    /**
     * @param int $area_id
     */
    public function setAreaId($area_id)
    {
        $this->area_id = $area_id;
    }

    /**
     * @return string
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * @param string $areas
     */
    public function setAreas($areas)
    {
        $this->areas = $areas;
    }

    /**
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->address_line1;
    }

    /**
     * @param string $address_line1
     */
    public function setAddressLine1($address_line1)
    {
        $this->address_line1 = $address_line1;
    }

    /**
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->address_line2;
    }

    /**
     * @param string $address_line2
     */
    public function setAddressLine2($address_line2)
    {
        $this->address_line2 = $address_line2;
    }

    /**
     * @return string
     */
    public function getAddressLine3()
    {
        return $this->address_line3;
    }

    /**
     * @param string $address_line3
     */
    public function setAddressLine3($address_line3)
    {
        $this->address_line3 = $address_line3;
    }

    /**
     * @return string
     */
    public function getAddressLine4()
    {
        return $this->address_line4;
    }

    /**
     * @param string $address_line4
     */
    public function setAddressLine4($address_line4)
    {
        $this->address_line4 = $address_line4;
    }

    /**
     * @return string
     */
    public function getAddressLine5()
    {
        return $this->address_line5;
    }

    /**
     * @param string $address_line5
     */
    public function setAddressLine5($address_line5)
    {
        $this->address_line5 = $address_line5;
    }

    /**
     * @return string
     */
    public function getAddressOther()
    {
        return $this->address_other;
    }

    /**
     * @param string $address_other
     */
    public function setAddressOther($address_other)
    {
        $this->address_other = $address_other;
    }

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return string
     */
    public function getFlatNumber()
    {
        return $this->flat_number;
    }

    /**
     * @param string $flat_number
     */
    public function setFlatNumber($flat_number)
    {
        $this->flat_number = $flat_number;
    }

    /**
     * @return string
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param string $structure
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;
    }

    /**
     * @return string
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param string $building
     */
    public function setBuilding($building)
    {
        $this->building = $building;
    }

    /**
     * @return string
     */
    public function getIntercom()
    {
        return $this->intercom;
    }

    /**
     * @param string $intercom
     */
    public function setIntercom($intercom)
    {
        $this->intercom = $intercom;
    }

    /**
     * @return string
     */
    public function getEntrance()
    {
        return $this->entrance;
    }

    /**
     * @param string $entrance
     */
    public function setEntrance($entrance)
    {
        $this->entrance = $entrance;
    }

    /**
     * @return string
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * @param string $floor
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;
    }

    /**
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param string $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
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
     * @return string
     */
    public function getFormattedCustomerAddress()
    {
        return $this->formatted_customer_address;
    }

    /**
     * @param string $formatted_customer_address
     */
    public function setFormattedCustomerAddress($formatted_customer_address)
    {
        $this->formatted_customer_address = $formatted_customer_address;
    }

    /**
     * @return string
     */
    public function getDeliveryInstructions()
    {
        return $this->delivery_instructions;
    }

    /**
     * @param string $delivery_instructions
     */
    public function setDeliveryInstructions($delivery_instructions)
    {
        $this->delivery_instructions = $delivery_instructions;
    }
}
