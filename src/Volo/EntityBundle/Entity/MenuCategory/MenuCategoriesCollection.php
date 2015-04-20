<?php

namespace Volo\EntityBundle\Entity\MenuCategory;

use Volo\EntityBundle\Entity\DataObjectCollection;

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
