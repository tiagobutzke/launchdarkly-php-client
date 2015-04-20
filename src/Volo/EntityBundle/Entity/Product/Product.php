<?php

namespace Volo\EntityBundle\Entity\Product;

use Volo\EntityBundle\Entity\DataObject;
use Volo\EntityBundle\Entity\ProductVariation\ProductVariationsCollection;

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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->file_path;
    }

    /**
     * @param string $file_path
     */
    public function setFilePath($file_path)
    {
        $this->file_path = $file_path;
    }

    /**
     * @return string
     */
    public function getHalfType()
    {
        return $this->half_type;
    }

    /**
     * @param string $half_type
     */
    public function setHalfType($half_type)
    {
        $this->half_type = $half_type;
    }

    /**
     * @return boolean
     */
    public function isIsHalfTypeAvailable()
    {
        return $this->is_half_type_available;
    }

    /**
     * @param boolean $is_half_type_available
     */
    public function setIsHalfTypeAvailable($is_half_type_available)
    {
        $this->is_half_type_available = $is_half_type_available;
    }

    /**
     * @return ProductVariationsCollection
     */
    public function getProductVariations()
    {
        return $this->product_variations;
    }

    /**
     * @param ProductVariationsCollection $product_variations
     */
    public function setProductVariations($product_variations)
    {
        $this->product_variations = $product_variations;
    }
}
