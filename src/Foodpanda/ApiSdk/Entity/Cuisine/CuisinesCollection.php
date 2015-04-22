<?php

namespace Foodpanda\ApiSdk\Entity\Cuisine;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class CuisinesCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return Cuisine::class;
    }
}
