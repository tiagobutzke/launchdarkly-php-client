<?php

namespace Foodpanda\ApiSdk\Entity\Vendor;

use Foodpanda\ApiSdk\Entity\Chain\Chain;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Entity\Cuisine\CuisinesCollection;
use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\Discount\DiscountsCollection;
use Foodpanda\ApiSdk\Entity\FoodCharacteristics\FoodCharacteristicsCollection;
use Foodpanda\ApiSdk\Entity\Menu\Menu;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return int
     */
    public function getReviewNumber()
    {
        return $this->review_number;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getPostCode()
    {
        return $this->post_code;
    }

    /**
     * @return float
     */
    public function getMinimumOrderAmount()
    {
        return $this->minimum_order_amount;
    }

    /**
     * @return float
     */
    public function getMinimumDeliveryFee()
    {
        return $this->minimum_delivery_fee;
    }

    /**
     * @return int
     */
    public function getMinimumDeliveryTime()
    {
        return $this->minimum_delivery_time;
    }

    /**
     * @return int
     */
    public function getMinimumPickupTime()
    {
        return $this->minimum_pickup_time;
    }

    /**
     * @return boolean
     */
    public function isIsDeliveryEnabled()
    {
        return $this->is_delivery_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsPickupEnabled()
    {
        return $this->is_pickup_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsPreorderEnabled()
    {
        return $this->is_preorder_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsVoucherEnabled()
    {
        return $this->is_voucher_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsVatDisabled()
    {
        return $this->is_vat_disabled;
    }

    /**
     * @return boolean
     */
    public function isIsVatVisible()
    {
        return $this->is_vat_visible;
    }

    /**
     * @return boolean
     */
    public function isIsVatIncludedInProductPrice()
    {
        return $this->is_vat_included_in_product_price;
    }

    /**
     * @return float
     */
    public function getVatPercentageAmount()
    {
        return $this->vat_percentage_amount;
    }

    /**
     * @return boolean
     */
    public function isIsServiceTaxEnabled()
    {
        return $this->is_service_tax_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsServiceTaxVisible()
    {
        return $this->is_service_tax_visible;
    }

    /**
     * @return float
     */
    public function getServiceTaxPercentageAmount()
    {
        return $this->service_tax_percentage_amount;
    }

    /**
     * @return boolean
     */
    public function isIsServiceFeeEnabled()
    {
        return $this->is_service_fee_enabled;
    }

    /**
     * @return float
     */
    public function getServiceFeePercentageAmount()
    {
        return $this->service_fee_percentage_amount;
    }

    /**
     * @return boolean
     */
    public function isIsCheckoutCommentEnabled()
    {
        return $this->is_checkout_comment_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsReplacementDishEnabled()
    {
        return $this->is_replacement_dish_enabled;
    }

    /**
     * @return string
     */
    public function getCustomLocationUrl()
    {
        return $this->custom_location_url;
    }

    /**
     * @return string
     */
    public function getCustomerType()
    {
        return $this->customer_type;
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        return $this->web_path;
    }

    /**
     * @return boolean
     */
    public function isIsTest()
    {
        return $this->is_test;
    }

    /**
     * @return string
     */
    public function getRedirectionUrl()
    {
        return $this->redirection_url;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return Chain
     */
    public function getChain()
    {
        return $this->chain;
    }

    /**
     * @return CuisinesCollection
     */
    public function getCuisines()
    {
        return $this->cuisines;
    }

    /**
     * @return FoodCharacteristicsCollection
     */
    public function getFoodCharacteristics()
    {
        return $this->food_characteristics;
    }

    /**
     * @return MetaData
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return DiscountsCollection
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @return SchedulesCollection
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * @return PaymentTypesCollection
     */
    public function getPaymentTypes()
    {
        return $this->payment_types;
    }

    /**
     * @return MenusCollection|Menu[]
     */
    public function getMenus()
    {
        return $this->menus;
    }
}
