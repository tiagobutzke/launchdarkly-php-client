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
     * @return string
     */
    public function getValueType()
    {
        return $this->value_type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getMaximumDiscountAmount()
    {
        return $this->maximum_discount_amount;
    }

    /**
     * @return string
     */
    public function getMaximumOrderValue()
    {
        return $this->maximum_order_value;
    }

    /**
     * @return string
     */
    public function getMinimumOrderValue()
    {
        return $this->minimum_order_value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getClosingHour()
    {
        return $this->closing_hour;
    }

    /**
     * @return string
     */
    public function getOpeningHour()
    {
        return $this->opening_hour;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @return string
     */
    public function getCustomerCode()
    {
        return $this->customer_code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
