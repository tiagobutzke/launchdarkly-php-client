<?php

namespace Foodpanda\ApiSdk\Entity\Language;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class LanguagesCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return Language::class;
    }
}
