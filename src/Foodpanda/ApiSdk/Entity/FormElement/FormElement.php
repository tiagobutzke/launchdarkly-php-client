<?php

namespace Foodpanda\ApiSdk\Entity\FormElement;

use Foodpanda\ApiSdk\Entity\DataObject;

class FormElement extends DataObject
{
    /**
     * @var string
     */
    protected $field_name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $keyboard_type;

    /**
     * @var string
     */
    protected $picker_type;

    /**
     * @var string
     */
    protected $validation;

    /**
     * @var bool
     */
    protected $is_required;

    /**
     * @var string
     */
    protected $default_value;

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getKeyboardType()
    {
        return $this->keyboard_type;
    }

    /**
     * @return string
     */
    public function getPickerType()
    {
        return $this->picker_type;
    }

    /**
     * @return string
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * @return boolean
     */
    public function isIsRequired()
    {
        return $this->is_required;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }
}
