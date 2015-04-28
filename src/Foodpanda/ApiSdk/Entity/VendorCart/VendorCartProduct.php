<?php

namespace Foodpanda\ApiSdk\Entity\VendorCart;

use Foodpanda\ApiSdk\Entity\Choice\ChoicesCollection;
use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\Topping\ToppingsCollection;

class VendorCartProduct extends DataObject
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
     * @param int $productVariationId
     */
    public function setProductVariationId($productVariationId)
    {
        $this->product_variation_id = $productVariationId;
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
     * @param string $variationName
     */
    public function setVariationName($variationName)
    {
        $this->variation_name = $variationName;
    }

    /**
     * @return float
     */
    public function getTotalPriceBeforeDiscount()
    {
        return $this->total_price_before_discount;
    }

    /**
     * @param float $totalPriceBeforeDiscount
     */
    public function setTotalPriceBeforeDiscount($totalPriceBeforeDiscount)
    {
        $this->total_price_before_discount = $totalPriceBeforeDiscount;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->total_price;
    }

    /**
     * @param float $totalPrice
     */
    public function setTotalPrice($totalPrice)
    {
        $this->total_price = $totalPrice;
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
     * @param string $groupOrderUserName
     */
    public function setGroupOrderUserName($groupOrderUserName)
    {
        $this->group_order_user_name = $groupOrderUserName;
    }

    /**
     * @return string
     */
    public function getGroupOrderUserCode()
    {
        return $this->group_order_user_code;
    }

    /**
     * @param string $groupOrderUserCode
     */
    public function setGroupOrderUserCode($groupOrderUserCode)
    {
        $this->group_order_user_code = $groupOrderUserCode;
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
