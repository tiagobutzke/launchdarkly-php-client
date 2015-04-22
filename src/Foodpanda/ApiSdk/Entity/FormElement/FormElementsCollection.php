<?php

namespace Foodpanda\ApiSdk\Entity\FormElement;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

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
