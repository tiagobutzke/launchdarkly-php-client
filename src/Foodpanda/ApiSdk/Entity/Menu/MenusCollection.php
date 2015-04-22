<?php

namespace Foodpanda\ApiSdk\Entity\Menu;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class MenusCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return Menu::class;
    }
}
