<?php

namespace Foodpanda\ApiSdk\Entity;

abstract class DataResultObject extends DataObject
{
    /**
     * @var int
     */
    protected $available_count;

    /**
     * @var int
     */
    protected $returned_count;

    /**
     * @var DataObjectCollection
     */
    protected $items;

    /**
     * @return int
     */
    public function getReturnedCount()
    {
        return $this->returned_count;
    }

    /**
     * @return int
     */
    public function getAvailableCount()
    {
        return $this->available_count;
    }

    /**
     * @return DataObjectCollection
     */
    abstract public function getItems();
}
