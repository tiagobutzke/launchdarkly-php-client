<?php

namespace Volo\EntityBundle\Entity\Choice;

use Volo\EntityBundle\Entity\DataObjectCollection;

class ChoicesCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return Choice::class;
    }
}
