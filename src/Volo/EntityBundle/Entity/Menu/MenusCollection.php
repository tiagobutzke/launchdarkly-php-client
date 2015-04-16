<?php

namespace Volo\EntityBundle\Entity\Menu;

use Volo\EntityBundle\Entity\DataObjectCollection;

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
