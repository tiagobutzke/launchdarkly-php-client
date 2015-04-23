<?php

namespace Foodpanda\ApiSdk\Entity\MenuCategory;

use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\Product\ProductsCollection;

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
    public function getMobifyFilePath()
    {
        return $this->mobify_file_path;
    }

    /**
     * @return boolean
     */
    public function isFileName()
    {
        return $this->file_name;
    }

    /**
     * @return ProductsCollection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
