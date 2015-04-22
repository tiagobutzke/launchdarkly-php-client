<?php

namespace Foodpanda\ApiSdk\Entity\BogoProductBlock;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class BogoProductBlocksCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return BogoProductBlock::class;
    }
}
