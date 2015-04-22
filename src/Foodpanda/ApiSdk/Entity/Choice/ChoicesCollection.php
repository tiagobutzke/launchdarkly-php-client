<?php

namespace Foodpanda\ApiSdk\Entity\Choice;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class ChoicesCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return Choice::class;
    }
}
