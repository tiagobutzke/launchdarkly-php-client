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
     * @param string $field_name
     */
    public function setFieldName($field_name)
    {
        $this->field_name = $field_name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getKeyboardType()
    {
        return $this->keyboard_type;
    }

    /**
     * @param string $keyboard_type
     */
    public function setKeyboardType($keyboard_type)
    {
        $this->keyboard_type = $keyboard_type;
    }

    /**
     * @return string
     */
    public function getPickerType()
    {
        return $this->picker_type;
    }

    /**
     * @param string $picker_type
     */
    public function setPickerType($picker_type)
    {
        $this->picker_type = $picker_type;
    }

    /**
     * @return string
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * @param string $validation
     */
    public function setValidation($validation)
    {
        $this->validation = $validation;
    }

    /**
     * @return boolean
     */
    public function isIsRequired()
    {
        return $this->is_required;
    }

    /**
     * @param boolean $is_required
     */
    public function setIsRequired($is_required)
    {
        $this->is_required = $is_required;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * @param string $default_value
     */
    public function setDefaultValue($default_value)
    {
        $this->default_value = $default_value;
    }
}
