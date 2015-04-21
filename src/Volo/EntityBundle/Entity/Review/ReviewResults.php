<?php

namespace Volo\EntityBundle\Entity\Review;

use Volo\EntityBundle\Entity\DataResultObject;

class ReviewResults extends DataResultObject
{
    public function __construct()
    {
        $this->items = new ReviewsCollection();
    }

    /**
     * @return ReviewsCollection|Review[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ReviewsCollection
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}
