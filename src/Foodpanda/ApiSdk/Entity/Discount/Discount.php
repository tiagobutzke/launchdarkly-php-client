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
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param DateTime $start_date
     */
    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @param DateTime $end_date
     */
    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;
    }

    /**
     * @return string
     */
    public function getConditionType()
    {
        return $this->condition_type;
    }

    /**
     * @param string $condition_type
     */
    public function setConditionType($condition_type)
    {
        $this->condition_type = $condition_type;
    }

    /**
     * @return int
     */
    public function getConditionObject()
    {
        return $this->condition_object;
    }

    /**
     * @param int $condition_object
     */
    public function setConditionObject($condition_object)
    {
        $this->condition_object = $condition_object;
    }

    /**
     * @return float
     */
    public function getMinimumOrderValue()
    {
        return $this->minimum_order_value;
    }

    /**
     * @param float $minimum_order_value
     */
    public function setMinimumOrderValue($minimum_order_value)
    {
        $this->minimum_order_value = $minimum_order_value;
    }

    /**
     * @return string
     */
    public function getDiscountType()
    {
        return $this->discount_type;
    }

    /**
     * @param string $discount_type
     */
    public function setDiscountType($discount_type)
    {
        $this->discount_type = $discount_type;
    }

    /**
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->discount_amount;
    }

    /**
     * @param float $discount_amount
     */
    public function setDiscountAmount($discount_amount)
    {
        $this->discount_amount = $discount_amount;
    }

    /**
     * @return float
     */
    public function getBogoDiscountUnit()
    {
        return $this->bogo_discount_unit;
    }

    /**
     * @param float $bogo_discount_unit
     */
    public function setBogoDiscountUnit($bogo_discount_unit)
    {
        $this->bogo_discount_unit = $bogo_discount_unit;
    }

    /**
     * @return string
     */
    public function getDiscountText()
    {
        return $this->discount_text;
    }

    /**
     * @param string $discount_text
     */
    public function setDiscountText($discount_text)
    {
        $this->discount_text = $discount_text;
    }

    /**
     * @return int
     */
    public function getVendorId()
    {
        return $this->vendor_id;
    }

    /**
     * @param int $vendor_id
     */
    public function setVendorId($vendor_id)
    {
        $this->vendor_id = $vendor_id;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * @param string $file_name
     */
    public function setFileName($file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * @return BogoProductBlocksCollection
     */
    public function getBogoProductBlocks()
    {
        return $this->bogo_product_blocks;
    }

    /**
     * @param BogoProductBlocksCollection $bogo_product_blocks
     */
    public function setBogoProductBlocks($bogo_product_blocks)
    {
        $this->bogo_product_blocks = $bogo_product_blocks;
    }
}
