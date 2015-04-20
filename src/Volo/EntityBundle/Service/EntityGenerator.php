<?php

namespace Volo\EntityBundle\Service;

use Symfony\Component\Serializer\Serializer;
use Volo\EntityBundle\Entity\Address\Address;
use Volo\EntityBundle\Entity\Address\AddressResults;
use Volo\EntityBundle\Entity\Cms\CmsResults;
use Volo\EntityBundle\Entity\Configuration\Configuration;
use Volo\EntityBundle\Entity\Customer\Customer;
use Volo\EntityBundle\Entity\Discount\DiscountResults;
use Volo\EntityBundle\Entity\OAuth\OAuth;
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
}
