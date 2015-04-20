<?php

namespace Volo\EntityBundle\Entity\BogoProductBlock;

use Volo\EntityBundle\Entity\DataObjectCollection;

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
