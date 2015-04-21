<?php

namespace Volo\EntityBundle\Entity\Review;

use Volo\EntityBundle\Entity\DataObjectCollection;

class ReviewsCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return Review::class;
    }
}
