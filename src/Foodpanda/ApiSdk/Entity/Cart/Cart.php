<?php

namespace Foodpanda\ApiSdk\Entity\Cart;

use Foodpanda\ApiSdk\Entity\DataObject;

class Cart extends DataObject
{
    /**
     * @var AbstractLocation
     */
    protected $location;

    /**
     * @var string
     */
    protected $expedition_type;

    /**
     * @var CartProductCollection|CartProduct[]
     */
    protected $products;

    /**
     * @param string $expeditionType
     * @param AbstractLocation $location
     * @param CartProduct[] $products
     */
    public function __construct($expeditionType = null, AbstractLocation $location = null, array $products = [])
    {
        $this->products = new CartProductCollection($products);
        $this->expedition_type = $expeditionType;
        $this->location = $location;
    }

    /**
     * @return AbstractLocation
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param AbstractLocation $location
     */
    public function setLocation(AbstractLocation $location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getExpeditionType()
    {
        return $this->expedition_type;
    }

    /**
     * @param string $expeditionType
     */
    public function setExpeditionType($expeditionType)
    {
        $this->expedition_type = $expeditionType;
    }

    /**
     * @return CartProduct[]|CartProductCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param CartProductCollection
     */
    public function setProducts(CartProductCollection $products)
    {
        $this->products = $products;
    }
}
