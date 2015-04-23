<?php

namespace Foodpanda\ApiSdk\Entity\Configuration;

use Foodpanda\ApiSdk\Entity\Customer\CustomerAddressConfiguration;
use Foodpanda\ApiSdk\Entity\Customer\CustomerConfiguration;
use Foodpanda\ApiSdk\Entity\FoodCharacteristics\FoodCharacteristicsCollection;
use Foodpanda\ApiSdk\Entity\Language\LanguagesCollection;
use Foodpanda\ApiSdk\Entity\Tracking\Tracking;
use Foodpanda\ApiSdk\Entity\DataObject;

class Configuration extends DataObject
{
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
        $this->customer_configuration = new CustomerConfiguration();
        $this->customer_address_configuration = new CustomerAddressConfiguration();
        $this->payment_form_configuration = new PaymentFormConfiguration();
        $this->enabled_social_connects = new SocialConnects();
    }

    /**
     * @return string
     */
    public function getLocationGroupType()
    {
        return $this->location_group_type;
    }

    /**
     * @return string
     */
    public function getLocationType()
    {
        return $this->location_type;
    }

    /**
     * @return string
     */
    public function getLocationHasCity()
    {
        return $this->location_has_city;
    }

    /**
     * @return boolean
     */
    public function isLocationHasArea()
    {
        return $this->location_has_area;
    }

    /**
     * @return boolean
     */
    public function isLocationHasSubarea()
    {
        return $this->location_has_subarea;
    }

    /**
     * @return boolean
     */
    public function isLocationHasAddress()
    {
        return $this->location_has_address;
    }

    /**
     * @return boolean
     */
    public function isLocationHasSeperateStreet()
    {
        return $this->location_has_seperate_street;
    }

    /**
     * @return string
     */
    public function getAdyenEncryptionPublicKey()
    {
        return $this->adyen_encryption_public_key;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @return string
     */
    public function getCountryCodeMobile()
    {
        return $this->country_code_mobile;
    }

    /**
     * @return boolean
     */
    public function isIsCountryCodeMobileVisible()
    {
        return $this->is_country_code_mobile_visible;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->currency_symbol;
    }

    /**
     * @return string
     */
    public function getCurrencySymbolIso()
    {
        return $this->currency_symbol_iso;
    }

    /**
     * @return string
     */
    public function getThousandsSeparator()
    {
        return $this->thousands_separator;
    }

    /**
     * @return string
     */
    public function getDecimalSeparator()
    {
        return $this->decimal_separator;
    }

    /**
     * @return int
     */
    public function getNumberOfDecimalDigits()
    {
        return $this->number_of_decimal_digits;
    }

    /**
     * @return string
     */
    public function getCurrencySymbolPosition()
    {
        return $this->currency_symbol_position;
    }

    /**
     * @return string
     */
    public function getMinimumOrderValue()
    {
        return $this->minimum_order_value;
    }

    /**
     * @return boolean
     */
    public function isHasCustomerSmsConfirmation()
    {
        return $this->has_customer_sms_confirmation;
    }

    /**
     * @return boolean
     */
    public function isIsTermsCheckboxVisible()
    {
        return $this->is_terms_checkbox_visible;
    }

    /**
     * @return boolean
     */
    public function isIsTermsCheckboxCheckedByDefault()
    {
        return $this->is_terms_checkbox_checked_by_default;
    }

    /**
     * @return LanguagesCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @return string
     */
    public function getDefaultLanguageCode()
    {
        return $this->default_language_code;
    }

    /**
     * @return int
     */
    public function getDefaultLanguageId()
    {
        return $this->default_language_id;
    }

    /**
     * @return boolean
     */
    public function isIsOpened()
    {
        return $this->is_opened;
    }

    /**
     * @return string
     */
    public function getOpensAt()
    {
        return $this->opens_at;
    }

    /**
     * @return CustomerConfiguration
     */
    public function getCustomerConfiguration()
    {
        return $this->customer_configuration;
    }

    /**
     * @return CustomerAddressConfiguration
     */
    public function getCustomerAddressConfiguration()
    {
        return $this->customer_address_configuration;
    }

    /**
     * @return PaymentFormConfiguration
     */
    public function getPaymentFormConfiguration()
    {
        return $this->payment_form_configuration;
    }

    /**
     * @return FoodCharacteristicsCollection
     */
    public function getFoodCharacteristicAvailableFilters()
    {
        return $this->food_characteristic_available_filters;
    }

    /**
     * @return SocialConnects
     */
    public function getEnabledSocialConnects()
    {
        return $this->enabled_social_connects;
    }

    /**
     * @return Tracking
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * @return string
     */
    public function getFacebookAppId()
    {
        return $this->facebook_app_id;
    }

    /**
     * @return boolean
     */
    public function isIsAbTestEnabled()
    {
        return $this->is_ab_test_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsAdyen3DSEnabled()
    {
        return $this->is_adyen3_d_s_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsAdyenRecurringEnabled()
    {
        return $this->is_adyen_recurring_enabled;
    }

    /**
     * @return boolean
     */
    public function isIsGroupOrderEnabled()
    {
        return $this->is_group_order_enabled;
    }
}
