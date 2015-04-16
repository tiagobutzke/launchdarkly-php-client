<?php

namespace Volo\EntityBundle\Entity\Event;

use Volo\EntityBundle\Entity\DataObjectCollection;

class EventsCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return Event::class;
    }
}
