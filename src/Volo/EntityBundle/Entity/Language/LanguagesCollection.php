<?php

namespace Volo\EntityBundle\Entity\Language;

use Volo\EntityBundle\Entity\DataObjectCollection;

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
