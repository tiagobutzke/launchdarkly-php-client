<?php

namespace Foodpanda\ApiSdk\Entity\Topping;

use Foodpanda\ApiSdk\Entity\DataObject;

class Topping extends DataObject
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var double
     */
    protected $price;

    /**
     * @var double
     */
    protected $price_before_discount;

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
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getPriceBeforeDiscount()
    {
        return $this->price_before_discount;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
