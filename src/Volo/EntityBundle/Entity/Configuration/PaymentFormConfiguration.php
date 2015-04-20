<?php

namespace Volo\EntityBundle\Entity\Configuration;

use Volo\EntityBundle\Entity\DataObject;
use Volo\EntityBundle\Entity\FormElement\FormElementsCollection;

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

    /**
     * @param FormElementsCollection $form_elements
     */
    public function setFormElements($form_elements)
    {
        $this->form_elements = $form_elements;
    }
}
