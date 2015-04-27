<?php

namespace Foodpanda\ApiSdk\Entity\Reorder;

use Foodpanda\ApiSdk\Entity\DataObject;

class ReorderVendor extends DataObject
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
    protected $logo;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var bool
     */
    protected $is_delivery_available;

    /**
     * @var int
     */
    protected $available_in;

    /**
     * @var bool
     */
    protected $is_pickup_available;

    /**
     * @var bool
     */
    protected $is_available;

    /**
     * @var bool
     */
    protected $is_preorder_available;

    /**
     * @var bool
     */
    protected $is_preorder_enabled;

    /**
     * @var string
     */
    protected $error_message;

    /**
     * @var string
     */
    protected $formatted_address;

    /**
     * @var ReorderProductsCollection
     */
    protected $products;

    /**
     * @var string
     */
    protected $filepath;

    public function __construct()
    {
        $this->products = new ReorderProductsCollection();
    }

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
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return boolean
     */
    public function isIsDeliveryAvailable()
    {
        return $this->is_delivery_available;
    }

    /**
     * @return int
     */
    public function getAvailableIn()
    {
        return $this->available_in;
    }

    /**
     * @return boolean
     */
    public function isIsPickupAvailable()
    {
        return $this->is_pickup_available;
    }

    /**
     * @return boolean
     */
    public function isIsAvailable()
    {
        return $this->is_available;
    }

    /**
     * @return boolean
     */
    public function isIsPreorderAvailable()
    {
        return $this->is_preorder_available;
    }

    /**
     * @return boolean
     */
    public function isIsPreorderEnabled()
    {
        return $this->is_preorder_enabled;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * @return string
     */
    public function getFormattedAddress()
    {
        return $this->formatted_address;
    }

    /**
     * @return ReorderProductsCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return string
     */
    public function getFilepath()
    {
        return $this->filepath;
    }
}
