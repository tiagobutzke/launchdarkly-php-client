<?php

namespace Foodpanda\ApiSdk\Entity\Schedule;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class SchedulesCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return Schedule::class;
    }
}
