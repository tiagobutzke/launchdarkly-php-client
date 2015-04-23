<?php

namespace Foodpanda\ApiSdk\Entity\VendorCart;

use Foodpanda\ApiSdk\Entity\Choice\ChoicesCollection;
use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\Topping\ToppingsCollection;

class Product extends DataObject
{
    /**
     * @var int
     */
    protected $product_variation_id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $variation_name;

    /**
     * @var double
     */
    protected $total_price_before_discount;

    /**
     * @var double
     */
    protected $total_price;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var string
     */
    protected $group_order_user_name;

    /**
     * @var string
     */
    protected $group_order_user_code;

    /**
     * @var ToppingsCollection
     */
    protected $toppings;

    /**
     * @var ChoicesCollection
     */
    protected $choices;

    public function __construct()
    {
        $this->choices = new ChoicesCollection();
        $this->toppings = new ToppingsCollection();
    }

    /**
     * @return int
     */
    public function getProductVariationId()
    {
        return $this->product_variation_id;
    }

    /**
     * @param int $product_variation_id
     */
    public function setProductVariationId($product_variation_id)
    {
        $this->product_variation_id = $product_variation_id;
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
     * @return string
     */
    public function getVariationName()
    {
        return $this->variation_name;
    }

    /**
     * @param string $variation_name
     */
    public function setVariationName($variation_name)
    {
        $this->variation_name = $variation_name;
    }

    /**
     * @return float
     */
    public function getTotalPriceBeforeDiscount()
    {
        return $this->total_price_before_discount;
    }

    /**
     * @param float $total_price_before_discount
     */
    public function setTotalPriceBeforeDiscount($total_price_before_discount)
    {
        $this->total_price_before_discount = $total_price_before_discount;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->total_price;
    }

    /**
     * @param float $total_price
     */
    public function setTotalPrice($total_price)
    {
        $this->total_price = $total_price;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getGroupOrderUserName()
    {
        return $this->group_order_user_name;
    }

    /**
     * @param string $group_order_user_name
     */
    public function setGroupOrderUserName($group_order_user_name)
    {
        $this->group_order_user_name = $group_order_user_name;
    }

    /**
     * @return string
     */
    public function getGroupOrderUserCode()
    {
        return $this->group_order_user_code;
    }

    /**
     * @param string $group_order_user_code
     */
    public function setGroupOrderUserCode($group_order_user_code)
    {
        $this->group_order_user_code = $group_order_user_code;
    }

    /**
     * @return ToppingsCollection
     */
    public function getToppings()
    {
        return $this->toppings;
    }

    /**
     * @param ToppingsCollection $toppings
     */
    public function setToppings($toppings)
    {
        $this->toppings = $toppings;
    }

    /**
     * @return ChoicesCollection
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param ChoicesCollection $choices
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    }
}
