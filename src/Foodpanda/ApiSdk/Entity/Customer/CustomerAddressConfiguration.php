<?php

namespace Foodpanda\ApiSdk\Entity\Customer;

use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\FormElement\FormElementsCollection;

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
     * @return FormElementsCollection
     */
    public function getFormElements()
    {
        return $this->form_elements;
    }
}
