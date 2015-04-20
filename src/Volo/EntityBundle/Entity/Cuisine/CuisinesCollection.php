<?php

namespace Volo\EntityBundle\Entity\Cuisine;

use Volo\EntityBundle\Entity\DataObjectCollection;

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
