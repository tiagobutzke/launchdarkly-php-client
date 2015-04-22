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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPriceBeforeDiscount()
    {
        return $this->price_before_discount;
    }

    /**
     * @param float $price_before_discount
     */
    public function setPriceBeforeDiscount($price_before_discount)
    {
        $this->price_before_discount = $price_before_discount;
    }

    /**
     * @return float
     */
    public function getContainerPrice()
    {
        return $this->container_price;
    }

    /**
     * @param float $container_price
     */
    public function setContainerPrice($container_price)
    {
        $this->container_price = $container_price;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param array $choices
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    }

    /**
     * @return array
     */
    public function getToppings()
    {
        return $this->toppings;
    }

    /**
     * @param array $toppings
     */
    public function setToppings($toppings)
    {
        $this->toppings = $toppings;
    }
}
