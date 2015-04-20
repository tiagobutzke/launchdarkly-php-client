<?php

namespace Volo\EntityBundle\Entity\Schedule;

use Volo\EntityBundle\Entity\DataObjectCollection;

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
