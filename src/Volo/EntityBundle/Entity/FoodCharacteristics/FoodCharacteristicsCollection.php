<?php

namespace Volo\EntityBundle\Entity\FoodCharacteristics;

use Volo\EntityBundle\Entity\DataObjectCollection;

class FoodCharacteristicsCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return FoodCharacteristics::class;
    }
}
