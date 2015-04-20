<?php

namespace Volo\EntityBundle\Entity\FormElement;

use Volo\EntityBundle\Entity\DataObjectCollection;

class FormElementsCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return FormElement::class;
    }
}
