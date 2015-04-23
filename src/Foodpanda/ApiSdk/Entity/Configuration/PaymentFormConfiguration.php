<?php

namespace Foodpanda\ApiSdk\Entity\Configuration;

use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\FormElement\FormElementsCollection;

class PaymentFormConfiguration extends DataObject
{
    /**
     * @var FormElementsCollection
     */
    protected $form_elements;

    public function __construct()
    {
        $this->form_elements = new FormElementsCollection();
    }

    /**
     * @return FormElementsCollection
     */
    public function getFormElements()
    {
        return $this->form_elements;
    }
}
