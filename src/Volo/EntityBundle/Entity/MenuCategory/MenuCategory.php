<?php

namespace Volo\EntityBundle\Entity\MenuCategory;

use Volo\EntityBundle\Entity\DataObject;
use Volo\EntityBundle\Entity\Product\ProductsCollection;

class MenuCategory extends DataObject
{
    /**
     * @var integer
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
    protected $mobify_file_path;

    /**
     * @var boolean
     */
    protected $file_name;

    /**
     * @var ProductsCollection
     */
    protected $products;

    public function __construct()
    {
        $this->products = new ProductsCollection();
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
    public function getMobifyFilePath()
    {
        return $this->mobify_file_path;
    }

    /**
     * @param string $mobify_file_path
     */
    public function setMobifyFilePath($mobify_file_path)
    {
        $this->mobify_file_path = $mobify_file_path;
    }

    /**
     * @return boolean
     */
    public function isFileName()
    {
        return $this->file_name;
    }

    /**
     * @param boolean $file_name
     */
    public function setFileName($file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * @return ProductsCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param ProductsCollection $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }
}
