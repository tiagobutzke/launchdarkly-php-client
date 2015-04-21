<?php

namespace Volo\EntityBundle\Service;

use Symfony\Component\Serializer\Serializer;
use Volo\EntityBundle\Entity\Address\Address;
use Volo\EntityBundle\Entity\Address\AddressResults;
use Volo\EntityBundle\Entity\City\City;
use Volo\EntityBundle\Entity\City\CityResults;
use Volo\EntityBundle\Entity\Geocoding\AreaResults;
use Volo\EntityBundle\Entity\Cms\CmsResults;
use Volo\EntityBundle\Entity\Configuration\Configuration;
use Volo\EntityBundle\Entity\Customer\Customer;
use Volo\EntityBundle\Entity\Customer\CustomerAddressConfiguration;
use Volo\EntityBundle\Entity\Customer\CustomerConfiguration;
use Volo\EntityBundle\Entity\Discount\DiscountResults;
use Volo\EntityBundle\Entity\Geocoding\ExtendedDetails;
use Volo\EntityBundle\Entity\OAuth\OAuth;
use Volo\EntityBundle\Entity\Order\PostCalculateResponse;
use Volo\EntityBundle\Entity\Order\PostOrderResponse;
use Volo\EntityBundle\Entity\Review\ReviewResults;
use Volo\EntityBundle\Entity\Vendor\Vendor;
use Volo\EntityBundle\Entity\Vendor\VendorResults;

class EntityGenerator
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array $data
     *
     * @return CmsResults
     */
    public function generateCms(array $data)
    {
        return $this->serializer->denormalize($data, CmsResults::class);
    }

    /**
     * @param array $data
     *
     * @return VendorResults
     */
    public function generateVendors(array $data)
    {
        return $this->serializer->denormalize($data, VendorResults::class);
    }

    /**
     * @param array $data
     *
     * @return Vendor
     */
    public function generateVendor(array $data)
    {
        return $this->serializer->denormalize($data, Vendor::class);
    }

    /**
     * @param array $data
     *
     * @return DiscountResults
     */
    public function generateDiscounts(array $data)
    {
        return $this->serializer->denormalize($data, DiscountResults::class);
    }

    /**
     * @param array $data
     *
     * @return Configuration
     */
    public function generateConfiguration(array $data)
    {
        return $this->serializer->denormalize($data, Configuration::class);
    }

    /**
     * @param array $data
     *
     * @return Customer
     */
    public function generateCustomer(array $data)
    {
        return $this->serializer->denormalize($data, Customer::class);
    }

    /**
     * @param array $data
     *
     * @return Address
     */
    public function generateCustomerAddress(array $data)
    {
        return $this->serializer->denormalize($data, Address::class);
    }

    /**
     * @param array $data
     *
     * @return AddressResults
     */
    public function generateCustomerAddresses(array $data)
    {
        return $this->serializer->denormalize($data, AddressResults::class);
    }

    /**
     * @param array $data
     *
     * @return OAuth
     */
    public function generateOAuth(array $data)
    {
        return $this->serializer->denormalize($data, OAuth::class);
    }

    /**
     * @param array $data
     *
     * @return CustomerConfiguration
     */
    public function generateCustomerConfiguration(array $data)
    {
        return $this->serializer->denormalize($data, CustomerConfiguration::class);
    }

    /**
     * @param array $data
     *
     * @return CustomerAddressConfiguration
     */
    public function generateCustomerAddressConfiguration(array $data)
    {
        return $this->serializer->denormalize($data, CustomerAddressConfiguration::class);
    }

    /**
     * @param array $data
     *
     * @return AreaResults
     */
    public function generateGeocodingAreas(array $data)
    {
        return $this->serializer->denormalize($data, AreaResults::class);
    }

    /**
     * @param array $data
     *
     * @return CityResults
     */
    public function generateCities(array $data)
    {
        return $this->serializer->denormalize($data, CityResults::class);
    }

    /**
     * @param array $data
     *
     * @return CityResults
     */
    public function generateGeocodingCities(array $data)
    {
        return $this->generateCities($data);
    }

    /**
     * @param array $data
     *
     * @return CityResults
     */
    public function generteInverseGeocodingAreas(array $data)
    {
        return $this->generateGeocodingCities($data);
    }

    /**
     * @param array $data
     *
     * @return \Volo\EntityBundle\Entity\Geocoding\AddressResults::class
     */
    public function generateGeocodingAddresses(array $data)
    {
        return $this->serializer->denormalize($data, \Volo\EntityBundle\Entity\Geocoding\AddressResults::class);
    }

    /**
     * @param array $data
     *
     * @return \Volo\EntityBundle\Entity\Geocoding\AddressResults
     */
    public function generateReverseGeocodingAddresses(array $data)
    {
        return $this->generateGeocodingAddresses($data);
    }

    /**
     * @param array $data
     *
     * @return ExtendedDetails
     */
    public function generateAddressExtendedDetails(array $data)
    {
        return $this->serializer->denormalize($data, ExtendedDetails::class);
    }

    /**
     * @param array $data
     *
     * @return City
     */
    public function generateCity(array $data)
    {
        return $this->serializer->denormalize($data, City::class);
    }

    /**
     * @param array $data
     *
     * @return ReviewResults
     */
    public function generateReviews(array $data)
    {
        return $this->serializer->denormalize($data, ReviewResults::class);
    }

    /**
     * @param array $data
     *
     * @return Customer
     */
    public function generateGuestCustomer(array $data)
    {
        return $this->generateCustomer($data);
    }

    /**
     * @param array $data
     *
     * @return PostOrderResponse
     */
    public function generatePostOrderResponse(array $data)
    {
        $this->serializer->denormalize($data, PostOrderResponse::class);
    }

    /**
     * @param array $data
     *
     * @return PostCalculateResponse
     */
    public function generatePostCalculateReponse(array $data)
    {
        $this->serializer->denormalize($data, PostCalculateResponse::class);
    }
}
