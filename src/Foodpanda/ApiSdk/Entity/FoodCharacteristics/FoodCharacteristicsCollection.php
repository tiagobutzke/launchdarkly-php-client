<?php

namespace Foodpanda\ApiSdk\Entity\FoodCharacteristics;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

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
