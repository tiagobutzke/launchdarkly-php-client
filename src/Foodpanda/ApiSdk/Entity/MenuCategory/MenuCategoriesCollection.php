<?php

namespace Foodpanda\ApiSdk\Entity\MenuCategory;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class MenuCategoriesCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return MenuCategory::class;
    }
}
