<?php

namespace Foodpanda\ApiSdk;

use Foodpanda\ApiSdk\Entity\Address\Address;
use Foodpanda\ApiSdk\Entity\Address\AddressResults;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Entity\City\CityResults;
use Foodpanda\ApiSdk\Entity\Cms\CmsResults;
use Foodpanda\ApiSdk\Entity\Configuration\Configuration;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\Customer\CustomerAddressConfiguration;
use Foodpanda\ApiSdk\Entity\Customer\CustomerConfiguration;
use Foodpanda\ApiSdk\Entity\Discount\DiscountResults;
use Foodpanda\ApiSdk\Entity\Geocoding\AreaResults;
use Foodpanda\ApiSdk\Entity\Geocoding\ExtendedDetails;
use Foodpanda\ApiSdk\Entity\OAuth\OAuth;
use Foodpanda\ApiSdk\Entity\Order\PostCalculateResponse;
use Foodpanda\ApiSdk\Entity\Order\PostOrderResponse;
use Foodpanda\ApiSdk\Entity\Review\ReviewResults;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Entity\Vendor\VendorResults;
use Symfony\Component\Serializer\Serializer as BaseSerializer;

class Serializer extends BaseSerializer
{
    /**
     * @param array $data
     *
     * @return CmsResults
     */
    public function denormalizeCms(array $data)
    {
        return  $this->denormalize($data, CmsResults::class);
    }

    /**
     * @param array $data
     *
     * @return VendorResults
     */
    public function denormalizeVendors(array $data)
    {
        return  $this->denormalize($data, VendorResults::class);
    }

    /**
     * @param array $data
     *
     * @return Vendor
     */
    public function denormalizeVendor(array $data)
    {
        return  $this->denormalize($data, Vendor::class);
    }

    /**
     * @param array $data
     *
     * @return DiscountResults
     */
    public function denormalizeDiscounts(array $data)
    {
        return  $this->denormalize($data, DiscountResults::class);
    }

    /**
     * @param array $data
     *
     * @return Configuration
     */
    public function denormalizeConfiguration(array $data)
    {
        return  $this->denormalize($data, Configuration::class);
    }

    /**
     * @param array $data
     *
     * @return Customer
     */
    public function denormalizeCustomer(array $data)
    {
        return  $this->denormalize($data, Customer::class);
    }

    /**
     * @param array $data
     *
     * @return Address
     */
    public function denormalizeCustomerAddress(array $data)
    {
        return  $this->denormalize($data, Address::class);
    }

    /**
     * @param array $data
     *
     * @return AddressResults
     */
    public function denormalizeCustomerAddresses(array $data)
    {
        return  $this->denormalize($data, AddressResults::class);
    }

    /**
     * @param array $data
     *
     * @return OAuth
     */
    public function denormalizeOAuth(array $data)
    {
        return  $this->denormalize($data, OAuth::class);
    }

    /**
     * @param array $data
     *
     * @return CustomerConfiguration
     */
    public function denormalizeCustomerConfiguration(array $data)
    {
        return  $this->denormalize($data, CustomerConfiguration::class);
    }

    /**
     * @param array $data
     *
     * @return CustomerAddressConfiguration
     */
    public function denormalizeCustomerAddressConfiguration(array $data)
    {
        return  $this->denormalize($data, CustomerAddressConfiguration::class);
    }

    /**
     * @param array $data
     *
     * @return AreaResults
     */
    public function denormalizeGeocodingAreas(array $data)
    {
        return  $this->denormalize($data, AreaResults::class);
    }

    /**
     * @param array $data
     *
     * @return CityResults
     */
    public function denormalizeCities(array $data)
    {
        return  $this->denormalize($data, CityResults::class);
    }

    /**
     * @param array $data
     *
     * @return CityResults
     */
    public function denormalizeGeocodingCities(array $data)
    {
        return $this->denormalizeCities($data);
    }

    /**
     * @param array $data
     *
     * @return CityResults
     */
    public function generteInverseGeocodingAreas(array $data)
    {
        return $this->denormalizeGeocodingCities($data);
    }

    /**
     * @param array $data
     *
     * @return \Foodpanda\ApiSdk\Entity\Geocoding\AddressResults::class
     */
    public function denormalizeGeocodingAddresses(array $data)
    {
        return  $this->denormalize($data, \Foodpanda\ApiSdk\Entity\Geocoding\AddressResults::class);

    }

    /**
     * @param array $data
     *
     * @return \Foodpanda\ApiSdk\Entity\Geocoding\AddressResults
     */
    public function denormalizeReverseGeocodingAddresses(array $data)
    {
        return $this->denormalize($data, \Foodpanda\ApiSdk\Entity\Geocoding\AddressResults::class);
    }

    /**
     * @param array $data
     *
     * @return ExtendedDetails
     */
    public function denormalizeAddressExtendedDetails(array $data)
    {
        return  $this->denormalize($data, ExtendedDetails::class);
    }

    /**
     * @param array $data
     *
     * @return City
     */
    public function denormalizeCity(array $data)
    {
        return  $this->denormalize($data, City::class);
    }

    /**
     * @param array $data
     *
     * @return ReviewResults
     */
    public function denormalizeReviews(array $data)
    {
        return  $this->denormalize($data, ReviewResults::class);
    }

    /**
     * @param array $data
     *
     * @return Customer
     */
    public function denormalizeGuestCustomer(array $data)
    {
        return $this->denormalizeCustomer($data);
    }

    /**
     * @param array $data
     *
     * @return PostOrderResponse
     */
    public function denormalizePostOrderResponse(array $data)
    {
         $this->denormalize($data, PostOrderResponse::class);
    }

    /**
     * @param array $data
     *
     * @return PostCalculateResponse
     */
    public function denormalizePostCalculateReponse(array $data)
    {
         $this->denormalize($data, PostCalculateResponse::class);
    }
}
