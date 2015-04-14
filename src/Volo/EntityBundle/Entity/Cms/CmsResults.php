<?php

namespace Volo\EntityBundle\Entity\Cms;

use Doctrine\Common\Collections\ArrayCollection;
use Volo\EntityBundle\Entity\DataObject;

class CmsResults extends DataObject
{
    /**
     * @var int
     */
    protected $returned_count;

    /**
     * @var CmsItemCollection
     */
    protected $items;

    public function __construct()
    {
        $this->items = new CmsItemCollection();
    }

    /**
     * @return int
     */
    public function getReturnedCount()
    {
        return $this->returned_count;
    }

    /**
     * @param int $returnedCount
     */
    public function setReturnedCount($returnedCount)
    {
        $this->returned_count = $returnedCount;
    }

    /**
     * @return ArrayCollection|CmsItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ArrayCollections<Item>
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}
