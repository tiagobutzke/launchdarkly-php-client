<?php

namespace Foodpanda\ApiSdk\Entity\Voucher;

use Foodpanda\ApiSdk\Entity\DataObject;

class Voucher extends DataObject
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $customer_code;

    /**
     * @var string
     */
    protected $start_date;

    /**
     * @var string
     */
    protected $end_date;

    /**
     * @var string
     */
    protected $opening_hour;

    /**
     * @var string
     */
    protected $closing_hour;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $minimum_order_value;

    /**
     * @var string
     */
    protected $maximum_order_value;

    /**
     * @var string
     */
    protected $maximum_discount_amount;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $value_type;

    /**
     * @var string
     */
    protected $free_gift_text;

    /**
     * @return string
     */
    public function getFreeGiftText()
    {
        return $this->free_gift_text;
    }

    /**
     * @param string $free_gift_text
     */
    public function setFreeGiftText($free_gift_text)
    {
        $this->free_gift_text = $free_gift_text;
    }

    /**
     * @return string
     */
    public function getValueType()
    {
        return $this->value_type;
    }

    /**
     * @param string $value_type
     */
    public function setValueType($value_type)
    {
        $this->value_type = $value_type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getMaximumDiscountAmount()
    {
        return $this->maximum_discount_amount;
    }

    /**
     * @param string $maximum_discount_amount
     */
    public function setMaximumDiscountAmount($maximum_discount_amount)
    {
        $this->maximum_discount_amount = $maximum_discount_amount;
    }

    /**
     * @return string
     */
    public function getMaximumOrderValue()
    {
        return $this->maximum_order_value;
    }

    /**
     * @param string $maximum_order_value
     */
    public function setMaximumOrderValue($maximum_order_value)
    {
        $this->maximum_order_value = $maximum_order_value;
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getClosingHour()
    {
        return $this->closing_hour;
    }

    /**
     * @param string $closing_hour
     */
    public function setClosingHour($closing_hour)
    {
        $this->closing_hour = $closing_hour;
    }

    /**
     * @return string
     */
    public function getOpeningHour()
    {
        return $this->opening_hour;
    }

    /**
     * @param string $opening_hour
     */
    public function setOpeningHour($opening_hour)
    {
        $this->opening_hour = $opening_hour;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @param string $end_date
     */
    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param string $start_date
     */
    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     * @return string
     */
    public function getCustomerCode()
    {
        return $this->customer_code;
    }

    /**
     * @param string $customer_code
     */
    public function setCustomerCode($customer_code)
    {
        $this->customer_code = $customer_code;
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
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
