<?php

namespace Volo\EntityBundle\Entity\Configuration;

use Volo\EntityBundle\Entity\Customer\CustomerAddressConfiguration;
use Volo\EntityBundle\Entity\Customer\CustomerConfiguration;
use Volo\EntityBundle\Entity\FoodCharacteristics\FoodCharacteristicsCollection;
use Volo\EntityBundle\Entity\Language\LanguagesCollection;
use Volo\EntityBundle\Entity\Tracking\Tracking;
use Volo\EntityBundle\Entity\DataObject;

class Configuration extends DataObject
{
    /**
     * @var array
     */
    protected $objectClasses = [
        'customer_configuration' => CustomerConfiguration::class,
        'customer_address_configuration' => CustomerAddressConfiguration::class,
        'payment_form_configuration' => PaymentFormConfiguration::class,
        'enabled_social_connects' => SocialConnects::class,
    ];

    /**
     * @var string
     */
    protected $location_group_type;

    /**
     * @var string
     */
    protected $location_type;

    /**
     * @var string
     */
    protected $location_has_city;

    /**
     * @var bool
     */
    protected $location_has_area;

    /**
     * @var bool
     */
    protected $location_has_subarea;

    /**
     * @var bool
     */
    protected $location_has_address;

    /**
     * @var bool
     */
    protected $location_has_seperate_street;

    /**
     * @var string
     */
    protected $adyen_encryption_public_key;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var string
     */
    protected $country_code_mobile;

    /**
     * @var bool
     */
    protected $is_country_code_mobile_visible;

    /**
     * @var string
     */
    protected $currency_symbol;

    /**
     * @var string
     */
    protected $currency_symbol_iso;

    /**
     * @var string
     */
    protected $thousands_separator;

    /**
     * @var string
     */
    protected $decimal_separator;

    /**
     * @var int
     */
    protected $number_of_decimal_digits;

    /**
     * @var string
     */
    protected $currency_symbol_position;

    /**
     * @var string
     */
    protected $minimum_order_value;

    /**
     * @var bool
     */
    protected $has_customer_sms_confirmation;

    /**
     * @var bool
     */
    protected $is_terms_checkbox_visible;

    /**
     * @var bool
     */
    protected $is_terms_checkbox_checked_by_default;

    /**
     * @var string
     */
    protected $default_language_code;

    /**
     * @var int
     */
    protected $default_language_id;

    /**
     * @var bool
     */
    protected $is_opened;

    /**
     * @var string
     */
    protected $opens_at;

    /**
     * @var string
     */
    protected $facebook_app_id;

    /**
     * @var bool
     */
    protected $is_ab_test_enabled;

    /**
     * @var bool
     */
    protected $is_adyen3_d_s_enabled;

    /**
     * @var bool
     */
    protected $is_adyen_recurring_enabled;

    /**
     * @var bool
     */
    protected $is_group_order_enabled;

    /**
     * @var CustomerConfiguration
     */
    protected $customer_configuration;

    /**
     * @var CustomerAddressConfiguration
     */
    protected $customer_address_configuration;

    /**
     * @var PaymentFormConfiguration
     */
    protected $payment_form_configuration;

    /**
     * @var SocialConnects
     */
    protected $enabled_social_connects;

    /**
     * @var LanguagesCollection
     */
    protected $languages;

    /**
     * @var FoodCharacteristicsCollection
     */
    protected $food_characteristic_available_filters;

    /**
     * @var Tracking
     */
    protected $tracking;

    public function __construct()
    {
        $this->languages = new LanguagesCollection();
        $this->food_characteristic_available_filters = new FoodCharacteristicsCollection();
    }

    /**
     * @return string
     */
    public function getLocationGroupType()
    {
        return $this->location_group_type;
    }

    /**
     * @param string $location_group_type
     */
    public function setLocationGroupType($location_group_type)
    {
        $this->location_group_type = $location_group_type;
    }

    /**
     * @return string
     */
    public function getLocationType()
    {
        return $this->location_type;
    }

    /**
     * @param string $location_type
     */
    public function setLocationType($location_type)
    {
        $this->location_type = $location_type;
    }

    /**
     * @return string
     */
    public function getLocationHasCity()
    {
        return $this->location_has_city;
    }

    /**
     * @param string $location_has_city
     */
    public function setLocationHasCity($location_has_city)
    {
        $this->location_has_city = $location_has_city;
    }

    /**
     * @return boolean
     */
    public function isLocationHasArea()
    {
        return $this->location_has_area;
    }

    /**
     * @param boolean $location_has_area
     */
    public function setLocationHasArea($location_has_area)
    {
        $this->location_has_area = $location_has_area;
    }

    /**
     * @return boolean
     */
    public function isLocationHasSubarea()
    {
        return $this->location_has_subarea;
    }

    /**
     * @param boolean $location_has_subarea
     */
    public function setLocationHasSubarea($location_has_subarea)
    {
        $this->location_has_subarea = $location_has_subarea;
    }

    /**
     * @return boolean
     */
    public function isLocationHasAddress()
    {
        return $this->location_has_address;
    }

    /**
     * @param boolean $location_has_address
     */
    public function setLocationHasAddress($location_has_address)
    {
        $this->location_has_address = $location_has_address;
    }

    /**
     * @return boolean
     */
    public function isLocationHasSeperateStreet()
    {
        return $this->location_has_seperate_street;
    }

    /**
     * @param boolean $location_has_seperate_street
     */
    public function setLocationHasSeperateStreet($location_has_seperate_street)
    {
        $this->location_has_seperate_street = $location_has_seperate_street;
    }

    /**
     * @return string
     */
    public function getAdyenEncryptionPublicKey()
    {
        return $this->adyen_encryption_public_key;
    }

    /**
     * @param string $adyen_encryption_public_key
     */
    public function setAdyenEncryptionPublicKey($adyen_encryption_public_key)
    {
        $this->adyen_encryption_public_key = $adyen_encryption_public_key;
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
     * @return string
     */
    public function getCountryCodeMobile()
    {
        return $this->country_code_mobile;
    }

    /**
     * @param string $country_code_mobile
     */
    public function setCountryCodeMobile($country_code_mobile)
    {
        $this->country_code_mobile = $country_code_mobile;
    }

    /**
     * @return boolean
     */
    public function isIsCountryCodeMobileVisible()
    {
        return $this->is_country_code_mobile_visible;
    }

    /**
     * @param boolean $is_country_code_mobile_visible
     */
    public function setIsCountryCodeMobileVisible($is_country_code_mobile_visible)
    {
        $this->is_country_code_mobile_visible = $is_country_code_mobile_visible;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->currency_symbol;
    }

    /**
     * @param string $currency_symbol
     */
    public function setCurrencySymbol($currency_symbol)
    {
        $this->currency_symbol = $currency_symbol;
    }

    /**
     * @return string
     */
    public function getCurrencySymbolIso()
    {
        return $this->currency_symbol_iso;
    }

    /**
     * @param string $currency_symbol_iso
     */
    public function setCurrencySymbolIso($currency_symbol_iso)
    {
        $this->currency_symbol_iso = $currency_symbol_iso;
    }

    /**
     * @return string
     */
    public function getThousandsSeparator()
    {
        return $this->thousands_separator;
    }

    /**
     * @param string $thousands_separator
     */
    public function setThousandsSeparator($thousands_separator)
    {
        $this->thousands_separator = $thousands_separator;
    }

    /**
     * @return string
     */
    public function getDecimalSeparator()
    {
        return $this->decimal_separator;
    }

    /**
     * @param string $decimal_separator
     */
    public function setDecimalSeparator($decimal_separator)
    {
        $this->decimal_separator = $decimal_separator;
    }

    /**
     * @return int
     */
    public function getNumberOfDecimalDigits()
    {
        return $this->number_of_decimal_digits;
    }

    /**
     * @param int $number_of_decimal_digits
     */
    public function setNumberOfDecimalDigits($number_of_decimal_digits)
    {
        $this->number_of_decimal_digits = $number_of_decimal_digits;
    }

    /**
     * @return string
     */
    public function getCurrencySymbolPosition()
    {
        return $this->currency_symbol_position;
    }

    /**
     * @param string $currency_symbol_position
     */
    public function setCurrencySymbolPosition($currency_symbol_position)
    {
        $this->currency_symbol_position = $currency_symbol_position;
    }

    /**
     * @return string
     */
    public function getMinimumOrderValue()
    {
        return $this->minimum_order_value;
    }

    /**
     * @param string $minimum_order_value
     */
    public function setMinimumOrderValue($minimum_order_value)
    {
        $this->minimum_order_value = $minimum_order_value;
    }

    /**
     * @return boolean
     */
    public function isHasCustomerSmsConfirmation()
    {
        return $this->has_customer_sms_confirmation;
    }

    /**
     * @param boolean $has_customer_sms_confirmation
     */
    public function setHasCustomerSmsConfirmation($has_customer_sms_confirmation)
    {
        $this->has_customer_sms_confirmation = $has_customer_sms_confirmation;
    }

    /**
     * @return boolean
     */
    public function isIsTermsCheckboxVisible()
    {
        return $this->is_terms_checkbox_visible;
    }

    /**
     * @param boolean $is_terms_checkbox_visible
     */
    public function setIsTermsCheckboxVisible($is_terms_checkbox_visible)
    {
        $this->is_terms_checkbox_visible = $is_terms_checkbox_visible;
    }

    /**
     * @return boolean
     */
    public function isIsTermsCheckboxCheckedByDefault()
    {
        return $this->is_terms_checkbox_checked_by_default;
    }

    /**
     * @param boolean $is_terms_checkbox_checked_by_default
     */
    public function setIsTermsCheckboxCheckedByDefault($is_terms_checkbox_checked_by_default)
    {
        $this->is_terms_checkbox_checked_by_default = $is_terms_checkbox_checked_by_default;
    }

    /**
     * @return LanguagesCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param LanguagesCollection $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return string
     */
    public function getDefaultLanguageCode()
    {
        return $this->default_language_code;
    }

    /**
     * @param string $default_language_code
     */
    public function setDefaultLanguageCode($default_language_code)
    {
        $this->default_language_code = $default_language_code;
    }

    /**
     * @return int
     */
    public function getDefaultLanguageId()
    {
        return $this->default_language_id;
    }

    /**
     * @param int $default_language_id
     */
    public function setDefaultLanguageId($default_language_id)
    {
        $this->default_language_id = $default_language_id;
    }

    /**
     * @return boolean
     */
    public function isIsOpened()
    {
        return $this->is_opened;
    }

    /**
     * @param boolean $is_opened
     */
    public function setIsOpened($is_opened)
    {
        $this->is_opened = $is_opened;
    }

    /**
     * @return string
     */
    public function getOpensAt()
    {
        return $this->opens_at;
    }

    /**
     * @param string $opens_at
     */
    public function setOpensAt($opens_at)
    {
        $this->opens_at = $opens_at;
    }

    /**
     * @return CustomerConfiguration
     */
    public function getCustomerConfiguration()
    {
        return $this->customer_configuration;
    }

    /**
     * @param CustomerConfiguration $customer_configuration
     */
    public function setCustomerConfiguration($customer_configuration)
    {
        $this->customer_configuration = $customer_configuration;
    }

    /**
     * @return CustomerAddressConfiguration
     */
    public function getCustomerAddressConfiguration()
    {
        return $this->customer_address_configuration;
    }

    /**
     * @param CustomerAddressConfiguration $customer_address_configuration
     */
    public function setCustomerAddressConfiguration($customer_address_configuration)
    {
        $this->customer_address_configuration = $customer_address_configuration;
    }

    /**
     * @return PaymentFormConfiguration
     */
    public function getPaymentFormConfiguration()
    {
        return $this->payment_form_configuration;
    }

    /**
     * @param PaymentFormConfiguration $payment_form_configuration
     */
    public function setPaymentFormConfiguration($payment_form_configuration)
    {
        $this->payment_form_configuration = $payment_form_configuration;
    }

    /**
     * @return FoodCharacteristicsCollection
     */
    public function getFoodCharacteristicAvailableFilters()
    {
        return $this->food_characteristic_available_filters;
    }

    /**
     * @param FoodCharacteristicsCollection $food_characteristic_available_filters
     */
    public function setFoodCharacteristicAvailableFilters($food_characteristic_available_filters)
    {
        $this->food_characteristic_available_filters = $food_characteristic_available_filters;
    }

    /**
     * @return SocialConnects
     */
    public function getEnabledSocialConnects()
    {
        return $this->enabled_social_connects;
    }

    /**
     * @param SocialConnects $enabled_social_connects
     */
    public function setEnabledSocialConnects($enabled_social_connects)
    {
        $this->enabled_social_connects = $enabled_social_connects;
    }

    /**
     * @return Tracking
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * @param Tracking $tracking
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;
    }

    /**
     * @return string
     */
    public function getFacebookAppId()
    {
        return $this->facebook_app_id;
    }

    /**
     * @param string $facebook_app_id
     */
    public function setFacebookAppId($facebook_app_id)
    {
        $this->facebook_app_id = $facebook_app_id;
    }

    /**
     * @return boolean
     */
    public function isIsAbTestEnabled()
    {
        return $this->is_ab_test_enabled;
    }

    /**
     * @param boolean $is_ab_test_enabled
     */
    public function setIsAbTestEnabled($is_ab_test_enabled)
    {
        $this->is_ab_test_enabled = $is_ab_test_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsAdyen3DSEnabled()
    {
        return $this->is_adyen3_d_s_enabled;
    }

    /**
     * @param boolean $is_adyen3_d_s_enabled
     */
    public function setIsAdyen3DSEnabled($is_adyen3_d_s_enabled)
    {
        $this->is_adyen3_d_s_enabled = $is_adyen3_d_s_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsAdyenRecurringEnabled()
    {
        return $this->is_adyen_recurring_enabled;
    }

    /**
     * @param boolean $is_adyen_recurring_enabled
     */
    public function setIsAdyenRecurringEnabled($is_adyen_recurring_enabled)
    {
        $this->is_adyen_recurring_enabled = $is_adyen_recurring_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsGroupOrderEnabled()
    {
        return $this->is_group_order_enabled;
    }

    /**
     * @param boolean $is_group_order_enabled
     */
    public function setIsGroupOrderEnabled($is_group_order_enabled)
    {
        $this->is_group_order_enabled = $is_group_order_enabled;
    }
}
