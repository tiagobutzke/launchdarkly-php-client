<?php

namespace Foodpanda\ApiSdk\Entity\Reorder;

use DateTime;
use Foodpanda\ApiSdk\Entity\DataObject;

class Reorder extends DataObject
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var float
     */
    protected $total_value;

    /**
     * @var ReorderVendorsCollection
     */
    protected $vendors;

    public function __construct()
    {
        $this->vendors = new ReorderVendorsCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return float
     */
    public function getTotalValue()
    {
        return $this->total_value;
    }

    /**
     * @return ReorderVendorsCollection
     */
    public function getVendors()
    {
        return $this->vendors;
    }
}
