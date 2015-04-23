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
     * @return string
     */
    public function getShortFormattedAddress()
    {
        return $this->short_formatted_address;
    }

    /**
     * @return string
     */
    public function getFormattedAddress()
    {
        return $this->formatted_address;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return string
     */
    public function getPostCode()
    {
        return $this->post_code;
    }

    /**
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->house_number;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @return int
     */
    public function getAreaId()
    {
        return $this->area_id;
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
}
