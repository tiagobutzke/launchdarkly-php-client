<?php

namespace Foodpanda\ApiSdk\Entity\Order;

use Foodpanda\ApiSdk\Entity\DataObject;

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
     * @return float
     */
    public function getTotalValue()
    {
        return $this->total_value;
    }

    /**
     * @return float
     */
    public function getDeliveryFee()
    {
        return $this->delivery_fee;
    }

    /**
     * @return int
     */
    public function getExpectedDeliveryDuration()
    {
        return $this->expected_delivery_duration;
    }

    /**
     * @return int
     */
    public function getNumberOfOrders()
    {
        return $this->number_of_orders;
    }

    /**
     * @return string
     */
    public function getExternalPaymentUrl()
    {
        return $this->external_payment_url;
    }
}
