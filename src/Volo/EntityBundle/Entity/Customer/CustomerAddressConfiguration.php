<?php

namespace Volo\EntityBundle\Entity\Customer;

use Volo\EntityBundle\Entity\DataObject;
use Volo\EntityBundle\Entity\FormElement\FormElementsCollection;

class CustomerAddressConfiguration extends DataObject
{
    /**
     * @var string
     */
    protected $list_template;

    /**
     * @var FormElementsCollection
     */
    protected $form_elements;

    public function __construct()
    {
        $this->form_elements = new FormElementsCollection();
    }

    /**
     * @return string
     */
    public function getListTemplate()
    {
        return $this->list_template;
    }

    /**
     * @param string $list_template
     */
    public function setListTemplate($list_template)
    {
        $this->list_template = $list_template;
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
