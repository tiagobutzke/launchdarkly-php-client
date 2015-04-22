<?php

namespace Foodpanda\ApiSdk\Entity\Geocoding;

use Foodpanda\ApiSdk\Entity\DataObject;

class Address extends DataObject
{
    /**
     * @var string
     */
    protected $city;

    /**
     * @var int
     */
    protected $city_id;

    /**
     * @var int
     */
    protected $area_id;

    /**
     * @var string
     */
    protected $district;

    /**
     * @var string
     */
    protected $street;

    /**
     * @var string
     */
    protected $house_number;

    /**
     * @var string
     */
    protected $post_code;

    /**
     * @var double
     */
    protected $latitude;

    /**
     * @var double
     */
    protected $longitude;

    /**
     * @var string
     */
    protected $formatted_address;

    /**
     * @var string
     */
    protected $short_formatted_address;

    /**
     * @var string
     */
    protected $extended_details_id;

    /**
     * @return string
     */
    public function getExtendedDetailsId()
    {
        return $this->extended_details_id;
    }

    /**
     * @param string $extended_details_id
     */
    public function setExtendedDetailsId($extended_details_id)
    {
        $this->extended_details_id = $extended_details_id;
    }

    /**
     * @return string
     */
    public function getShortFormattedAddress()
    {
        return $this->short_formatted_address;
    }

    /**
     * @param string $short_formatted_address
     */
    public function setShortFormattedAddress($short_formatted_address)
    {
        $this->short_formatted_address = $short_formatted_address;
    }

    /**
     * @return string
     */
    public function getFormattedAddress()
    {
        return $this->formatted_address;
    }

    /**
     * @param string $formatted_address
     */
    public function setFormattedAddress($formatted_address)
    {
        $this->formatted_address = $formatted_address;
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
     * @return string
     */
    public function getPostCode()
    {
        return $this->post_code;
    }

    /**
     * @param string $post_code
     */
    public function setPostCode($post_code)
    {
        $this->post_code = $post_code;
    }

    /**
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->house_number;
    }

    /**
     * @param string $house_number
     */
    public function setHouseNumber($house_number)
    {
        $this->house_number = $house_number;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
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
}
