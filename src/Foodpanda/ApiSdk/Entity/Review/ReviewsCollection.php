<?php

namespace Foodpanda\ApiSdk\Entity\Review;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class ReviewsCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return Review::class;
    }
}
