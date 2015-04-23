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
     * @return int
     */
    public function getObjectId()
    {
        return $this->object_id;
    }

    /**
     * @return string
     */
    public function getObjectCode()
    {
        return $this->object_code;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
