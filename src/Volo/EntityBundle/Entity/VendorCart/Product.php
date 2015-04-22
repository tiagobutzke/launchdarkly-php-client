<?php

namespace Volo\EntityBundle\Entity\VendorCart;

use Volo\EntityBundle\Entity\Choice\ChoicesCollection;
use Volo\EntityBundle\Entity\DataObject;
use Volo\EntityBundle\Entity\Topping\ToppingsCollection;

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
}
