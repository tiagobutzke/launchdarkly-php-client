<?php

namespace Volo\EntityBundle\Entity;

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
     * @param int $returned_count
     */
    public function setReturnedCount($returned_count)
    {
        $this->returned_count = $returned_count;
    }

    /**
     * @return int
     */
    public function getAvailableCount()
    {
        return $this->available_count;
    }

    /**
     * @param int $available_count
     */
    public function setAvailableCount($available_count)
    {
        $this->available_count = $available_count;
    }

    /**
     * @return DataObjectCollection
     */
    abstract public function getItems();
}
