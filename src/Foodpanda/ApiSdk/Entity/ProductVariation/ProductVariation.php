<?php

namespace Foodpanda\ApiSdk\Entity\ProductVariation;

use Foodpanda\ApiSdk\Entity\DataObject;

class ProductVariation extends DataObject
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $price_before_discount;

    /**
     * @var float
     */
    protected $container_price;

    /**
     * @var array
     */
    protected $choices = [];

    /**
     * @var array
     */
    protected $toppings = [];

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
    public function getCode()
    {
        return $this->code;
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
     * @return float
     */
    public function getContainerPrice()
    {
        return $this->container_price;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @return array
     */
    public function getToppings()
    {
        return $this->toppings;
    }
}
