<?php

namespace Foodpanda\ApiSdk\Entity\Discount;

use DateTime;
use Foodpanda\ApiSdk\Entity\BogoProductBlock\BogoProductBlocksCollection;
use Foodpanda\ApiSdk\Entity\DataObject;

class Discount extends DataObject
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
     * @var DateTime
     */
    protected $start_date;


    /**
     * @var DateTime
     */
    protected $end_date;

    /**
     * @var string
     */
    protected $condition_type;

    /**
     * @var int
     */
    protected $condition_object;

    /**
     * @var double
     */
    protected $minimum_order_value;

    /**
     * @var string
     */
    protected $discount_type;

    /**
     * @var double
     */
    protected $discount_amount;

    /**
     * @var double
     */
    protected $bogo_discount_unit;

    /**
     * @var string
     */
    protected $discount_text;

    /**
     * @var int
     */
    protected $vendor_id;

    /**
     * @var string
     */
    protected $file_name;

    /**
     * @var BogoProductBlocksCollection
     */
    protected $bogo_product_blocks;

    public function __construct()
    {
        $this->bogo_product_blocks = new BogoProductBlocksCollection();
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
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @return string
     */
    public function getConditionType()
    {
        return $this->condition_type;
    }

    /**
     * @return int
     */
    public function getConditionObject()
    {
        return $this->condition_object;
    }

    /**
     * @return float
     */
    public function getMinimumOrderValue()
    {
        return $this->minimum_order_value;
    }

    /**
     * @return string
     */
    public function getDiscountType()
    {
        return $this->discount_type;
    }

    /**
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->discount_amount;
    }

    /**
     * @return float
     */
    public function getBogoDiscountUnit()
    {
        return $this->bogo_discount_unit;
    }

    /**
     * @return string
     */
    public function getDiscountText()
    {
        return $this->discount_text;
    }

    /**
     * @return int
     */
    public function getVendorId()
    {
        return $this->vendor_id;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * @return BogoProductBlocksCollection
     */
    public function getBogoProductBlocks()
    {
        return $this->bogo_product_blocks;
    }
}
