<?php

namespace Foodpanda\ApiSdk\Entity\Product;

use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\ProductVariation\ProductVariationsCollection;

class Product extends DataObject
{
    /**
     * @var bool
     */
    protected $is_half_type_available;

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
    protected $code;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $file_path;

    /**
     * @var string
     */
    protected $half_type;

    /**
     * @var ProductVariationsCollection
     */
    protected $product_variations;

    public function __construct()
    {
        $this->product_variations = new ProductVariationsCollection();
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->file_path;
    }

    /**
     * @return string
     */
    public function getHalfType()
    {
        return $this->half_type;
    }

    /**
     * @return boolean
     */
    public function isIsHalfTypeAvailable()
    {
        return $this->is_half_type_available;
    }

    /**
     * @return ProductVariationsCollection
     */
    public function getProductVariations()
    {
        return $this->product_variations;
    }
}
