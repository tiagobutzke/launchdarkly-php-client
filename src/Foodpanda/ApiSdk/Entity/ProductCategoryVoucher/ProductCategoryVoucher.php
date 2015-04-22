<?php

namespace Foodpanda\ApiSdk\Entity\ProductCategoryVoucher;

use Foodpanda\ApiSdk\Entity\DataObject;

class ProductCategoryVoucher extends DataObject
{
    /**
     * @var string
     */
    protected $object_type;

    /**
     * @var int
     */
    protected $object_id;

    /**
     * @var string
     */
    protected $object_code;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @return string
     */
    public function getObjectType()
    {
        return $this->object_type;
    }

    /**
     * @param string $object_type
     */
    public function setObjectType($object_type)
    {
        $this->object_type = $object_type;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->object_id;
    }

    /**
     * @param int $object_id
     */
    public function setObjectId($object_id)
    {
        $this->object_id = $object_id;
    }

    /**
     * @return string
     */
    public function getObjectCode()
    {
        return $this->object_code;
    }

    /**
     * @param string $object_code
     */
    public function setObjectCode($object_code)
    {
        $this->object_code = $object_code;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
}
