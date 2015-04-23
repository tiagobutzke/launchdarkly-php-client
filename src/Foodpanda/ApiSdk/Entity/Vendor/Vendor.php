<?php

namespace Foodpanda\ApiSdk\Entity\Vendor;

use Foodpanda\ApiSdk\Entity\Chain\Chain;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Entity\Cuisine\CuisinesCollection;
use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\Discount\DiscountsCollection;
use Foodpanda\ApiSdk\Entity\FoodCharacteristics\FoodCharacteristicsCollection;
use Foodpanda\ApiSdk\Entity\Menu\MenusCollection;
use Foodpanda\ApiSdk\Entity\PaymentType\PaymentTypesCollection;
use Foodpanda\ApiSdk\Entity\Schedule\SchedulesCollection;

class Vendor extends DataObject
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var City
     */
    protected $city;

    /**
     * @var Chain
     */
    protected $chain;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var float
     */
    protected $latitude;

    /**
     * @var float
     */
    protected $longitude;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $logo;

    /**
     * @var float
     */
    protected $rating;

    /**
     * @var int
     */
    protected $review_number;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $post_code;

    /**
     * @var float
     */
    protected $minimum_order_amount;

    /**
     * @var float
     */
    protected $minimum_delivery_fee;

    /**
     * @var int
     */
    protected $minimum_delivery_time;

    /**
     * @var int
     */
    protected $minimum_pickup_time;

    /**
     * @var bool
     */
    protected $is_delivery_enabled;

    /**
     * @var bool
     */
    protected $is_pickup_enabled;

    /**
     * @var bool
     */
    protected $is_preorder_enabled;

    /**
     * @var bool
     */
    protected $is_voucher_enabled;
    /**
     * @var bool
     */
    protected $is_vat_disabled;

    /**
     * @var bool
     */
    protected $is_vat_visible;

    /**
     * @var bool
     */
    protected $is_vat_included_in_product_price;

    /**
     * @var float
     */
    protected $vat_percentage_amount;

    /**
     * @var bool
     */
    protected $is_service_tax_enabled;

    /**
     * @var bool
     */
    protected $is_service_tax_visible;

    /**
     * @var float
     */
    protected $service_tax_percentage_amount;

    /**
     * @var bool
     */
    protected $is_service_fee_enabled;

    /**
     * @var float
     */
    protected $service_fee_percentage_amount;
    /**
     * @var bool
     */
    protected $is_checkout_comment_enabled;

    /**
     * @var bool
     */
    protected $is_replacement_dish_enabled;

    /**
     * @var string
     */
    protected $custom_location_url;

    /**
     * @var string
     */
    protected $customer_type;

    /**
     * @var string
     */
    protected $web_path;

    /**
     * @var bool
     */
    protected $is_test;

    /**
     * @var string
     */
    protected $redirection_url;

    /**
     * @var MetaData
     */
    protected $metadata;

    /**
     * @var FoodCharacteristicsCollection
     */
    protected $food_characteristics;

    /**
     * @var CuisinesCollection
     */
    protected $cuisines;

    /**
     * @var DiscountsCollection
     */
    protected $discounts;

    /**
     * @var SchedulesCollection
     */
    protected $schedules;

    /**
     * @var PaymentTypesCollection
     */
    protected $payment_types;

    /**
     * @var MenusCollection
     */
    protected $menus;

    public function __construct()
    {
        $this->city = new City();
        $this->chain = new Chain();
        $this->metadata = new MetaData();

        $this->menus = new MenusCollection();
        $this->cuisines = new CuisinesCollection();
        $this->schedules = new SchedulesCollection();
        $this->discounts = new DiscountsCollection();
        $this->payment_types = new PaymentTypesCollection();
        $this->food_characteristics = new FoodCharacteristicsCollection();
    }

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return int
     */
    public function getReviewNumber()
    {
        return $this->review_number;
    }

    /**
     * @param int $review_number
     */
    public function setReviewNumber($review_number)
    {
        $this->review_number = $review_number;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
     * @return float
     */
    public function getMinimumOrderAmount()
    {
        return $this->minimum_order_amount;
    }

    /**
     * @param float $minimum_order_amount
     */
    public function setMinimumOrderAmount($minimum_order_amount)
    {
        $this->minimum_order_amount = $minimum_order_amount;
    }

    /**
     * @return float
     */
    public function getMinimumDeliveryFee()
    {
        return $this->minimum_delivery_fee;
    }

    /**
     * @param float $minimum_delivery_fee
     */
    public function setMinimumDeliveryFee($minimum_delivery_fee)
    {
        $this->minimum_delivery_fee = $minimum_delivery_fee;
    }

    /**
     * @return int
     */
    public function getMinimumDeliveryTime()
    {
        return $this->minimum_delivery_time;
    }

    /**
     * @param int $minimum_delivery_time
     */
    public function setMinimumDeliveryTime($minimum_delivery_time)
    {
        $this->minimum_delivery_time = $minimum_delivery_time;
    }

    /**
     * @return int
     */
    public function getMinimumPickupTime()
    {
        return $this->minimum_pickup_time;
    }

    /**
     * @param int $minimum_pickup_time
     */
    public function setMinimumPickupTime($minimum_pickup_time)
    {
        $this->minimum_pickup_time = $minimum_pickup_time;
    }

    /**
     * @return boolean
     */
    public function isIsDeliveryEnabled()
    {
        return $this->is_delivery_enabled;
    }

    /**
     * @param boolean $is_delivery_enabled
     */
    public function setIsDeliveryEnabled($is_delivery_enabled)
    {
        $this->is_delivery_enabled = $is_delivery_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsPickupEnabled()
    {
        return $this->is_pickup_enabled;
    }

    /**
     * @param boolean $is_pickup_enabled
     */
    public function setIsPickupEnabled($is_pickup_enabled)
    {
        $this->is_pickup_enabled = $is_pickup_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsPreorderEnabled()
    {
        return $this->is_preorder_enabled;
    }

    /**
     * @param boolean $is_preorder_enabled
     */
    public function setIsPreorderEnabled($is_preorder_enabled)
    {
        $this->is_preorder_enabled = $is_preorder_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsVoucherEnabled()
    {
        return $this->is_voucher_enabled;
    }

    /**
     * @param boolean $is_voucher_enabled
     */
    public function setIsVoucherEnabled($is_voucher_enabled)
    {
        $this->is_voucher_enabled = $is_voucher_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsVatDisabled()
    {
        return $this->is_vat_disabled;
    }

    /**
     * @param boolean $is_vat_disabled
     */
    public function setIsVatDisabled($is_vat_disabled)
    {
        $this->is_vat_disabled = $is_vat_disabled;
    }

    /**
     * @return boolean
     */
    public function isIsVatVisible()
    {
        return $this->is_vat_visible;
    }

    /**
     * @param boolean $is_vat_visible
     */
    public function setIsVatVisible($is_vat_visible)
    {
        $this->is_vat_visible = $is_vat_visible;
    }

    /**
     * @return boolean
     */
    public function isIsVatIncludedInProductPrice()
    {
        return $this->is_vat_included_in_product_price;
    }

    /**
     * @param boolean $is_vat_included_in_product_price
     */
    public function setIsVatIncludedInProductPrice($is_vat_included_in_product_price)
    {
        $this->is_vat_included_in_product_price = $is_vat_included_in_product_price;
    }

    /**
     * @return float
     */
    public function getVatPercentageAmount()
    {
        return $this->vat_percentage_amount;
    }

    /**
     * @param float $vat_percentage_amount
     */
    public function setVatPercentageAmount($vat_percentage_amount)
    {
        $this->vat_percentage_amount = $vat_percentage_amount;
    }

    /**
     * @return boolean
     */
    public function isIsServiceTaxEnabled()
    {
        return $this->is_service_tax_enabled;
    }

    /**
     * @param boolean $is_service_tax_enabled
     */
    public function setIsServiceTaxEnabled($is_service_tax_enabled)
    {
        $this->is_service_tax_enabled = $is_service_tax_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsServiceTaxVisible()
    {
        return $this->is_service_tax_visible;
    }

    /**
     * @param boolean $is_service_tax_visible
     */
    public function setIsServiceTaxVisible($is_service_tax_visible)
    {
        $this->is_service_tax_visible = $is_service_tax_visible;
    }

    /**
     * @return float
     */
    public function getServiceTaxPercentageAmount()
    {
        return $this->service_tax_percentage_amount;
    }

    /**
     * @param float $service_tax_percentage_amount
     */
    public function setServiceTaxPercentageAmount($service_tax_percentage_amount)
    {
        $this->service_tax_percentage_amount = $service_tax_percentage_amount;
    }

    /**
     * @return boolean
     */
    public function isIsServiceFeeEnabled()
    {
        return $this->is_service_fee_enabled;
    }

    /**
     * @param boolean $is_service_fee_enabled
     */
    public function setIsServiceFeeEnabled($is_service_fee_enabled)
    {
        $this->is_service_fee_enabled = $is_service_fee_enabled;
    }

    /**
     * @return float
     */
    public function getServiceFeePercentageAmount()
    {
        return $this->service_fee_percentage_amount;
    }

    /**
     * @param float $service_fee_percentage_amount
     */
    public function setServiceFeePercentageAmount($service_fee_percentage_amount)
    {
        $this->service_fee_percentage_amount = $service_fee_percentage_amount;
    }

    /**
     * @return boolean
     */
    public function isIsCheckoutCommentEnabled()
    {
        return $this->is_checkout_comment_enabled;
    }

    /**
     * @param boolean $is_checkout_comment_enabled
     */
    public function setIsCheckoutCommentEnabled($is_checkout_comment_enabled)
    {
        $this->is_checkout_comment_enabled = $is_checkout_comment_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsReplacementDishEnabled()
    {
        return $this->is_replacement_dish_enabled;
    }

    /**
     * @param boolean $is_replacement_dish_enabled
     */
    public function setIsReplacementDishEnabled($is_replacement_dish_enabled)
    {
        $this->is_replacement_dish_enabled = $is_replacement_dish_enabled;
    }

    /**
     * @return string
     */
    public function getCustomLocationUrl()
    {
        return $this->custom_location_url;
    }

    /**
     * @param string $custom_location_url
     */
    public function setCustomLocationUrl($custom_location_url)
    {
        $this->custom_location_url = $custom_location_url;
    }

    /**
     * @return string
     */
    public function getCustomerType()
    {
        return $this->customer_type;
    }

    /**
     * @param string $customer_type
     */
    public function setCustomerType($customer_type)
    {
        $this->customer_type = $customer_type;
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        return $this->web_path;
    }

    /**
     * @param string $web_path
     */
    public function setWebPath($web_path)
    {
        $this->web_path = $web_path;
    }

    /**
     * @return boolean
     */
    public function isIsTest()
    {
        return $this->is_test;
    }

    /**
     * @param boolean $is_test
     */
    public function setIsTest($is_test)
    {
        $this->is_test = $is_test;
    }

    /**
     * @return string
     */
    public function getRedirectionUrl()
    {
        return $this->redirection_url;
    }

    /**
     * @param string $redirection_url
     */
    public function setRedirectionUrl($redirection_url)
    {
        $this->redirection_url = $redirection_url;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return Chain
     */
    public function getChain()
    {
        return $this->chain;
    }

    /**
     * @param Chain $chain
     */
    public function setChain($chain)
    {
        $this->chain = $chain;
    }

    /**
     * @return CuisinesCollection
     */
    public function getCuisines()
    {
        return $this->cuisines;
    }

    /**
     * @param CuisinesCollection $cuisines
     */
    public function setCuisines($cuisines)
    {
        $this->cuisines = $cuisines;
    }

    /**
     * @return FoodCharacteristicsCollection
     */
    public function getFoodCharacteristics()
    {
        return $this->food_characteristics;
    }

    /**
     * @param FoodCharacteristicsCollection $food_characteristics
     */
    public function setFoodCharacteristics($food_characteristics)
    {
        $this->food_characteristics = $food_characteristics;
    }

    /**
     * @return MetaData
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param MetaData $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return DiscountsCollection
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @param DiscountsCollection $discounts
     */
    public function setDiscounts($discounts)
    {
        $this->discounts = $discounts;
    }

    /**
     * @return SchedulesCollection
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * @param SchedulesCollection $schedules
     */
    public function setSchedules($schedules)
    {
        $this->schedules = $schedules;
    }

    /**
     * @return PaymentTypesCollection
     */
    public function getPaymentTypes()
    {
        return $this->payment_types;
    }

    /**
     * @param PaymentTypesCollection $payment_types
     */
    public function setPaymentTypes($payment_types)
    {
        $this->payment_types = $payment_types;
    }

    /**
     * @return MenusCollection
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * @param MenusCollection $menus
     */
    public function setMenus($menus)
    {
        $this->menus = $menus;
    }
}
