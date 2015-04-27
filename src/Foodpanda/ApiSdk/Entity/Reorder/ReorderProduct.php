<?php

namespace Foodpanda\ApiSdk\Entity\Reorder;

use Foodpanda\ApiSdk\Entity\DataObject;

class ReorderProduct extends DataObject
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
    protected $description;

    /**
     * @var string
     */
    protected $file_path;

    /**
     * @var bool
     */
    protected $is_available;

    /**
     * @var string
     */
    protected $error_message;

    /**
     * @var ReorderProductVariation
     */
    protected $product_variation;

    public function __construct()
    {
        $this->product_variation = new ReorderProductVariation();
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
     * @return boolean
     */
    public function isIsAvailable()
    {
        return $this->is_available;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * @return ReorderProductVariation
     */
    public function getProductVariation()
    {
        return $this->product_variation;
    }
}
