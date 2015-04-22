<?php

namespace Volo\EntityBundle\Entity\Order;

use Volo\EntityBundle\Entity\DataObject;

class PostOrderResponse extends DataObject
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var double
     */
    protected $total_value;

    /**
     * @var double
     */
    protected $delivery_fee;

    /**
     * @var int
     */
    protected $expected_delivery_duration;

    /**
     * @var int
     */
    protected $number_of_orders;

    /**
     * @var string
     */
    protected $external_payment_url;

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
    public function getTotalValue()
    {
        return $this->total_value;
    }

    /**
     * @param float $total_value
     */
    public function setTotalValue($total_value)
    {
        $this->total_value = $total_value;
    }

    /**
     * @return float
     */
    public function getDeliveryFee()
    {
        return $this->delivery_fee;
    }

    /**
     * @param float $delivery_fee
     */
    public function setDeliveryFee($delivery_fee)
    {
        $this->delivery_fee = $delivery_fee;
    }

    /**
     * @return int
     */
    public function getExpectedDeliveryDuration()
    {
        return $this->expected_delivery_duration;
    }

    /**
     * @param int $expected_delivery_duration
     */
    public function setExpectedDeliveryDuration($expected_delivery_duration)
    {
        $this->expected_delivery_duration = $expected_delivery_duration;
    }

    /**
     * @return int
     */
    public function getNumberOfOrders()
    {
        return $this->number_of_orders;
    }

    /**
     * @param int $number_of_orders
     */
    public function setNumberOfOrders($number_of_orders)
    {
        $this->number_of_orders = $number_of_orders;
    }

    /**
     * @return string
     */
    public function getExternalPaymentUrl()
    {
        return $this->external_payment_url;
    }

    /**
     * @param string $external_payment_url
     */
    public function setExternalPaymentUrl($external_payment_url)
    {
        $this->external_payment_url = $external_payment_url;
    }
}
