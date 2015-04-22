<?php

namespace Foodpanda\ApiSdk\Entity\Event;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

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
