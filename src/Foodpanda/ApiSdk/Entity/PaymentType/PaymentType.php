<?php

namespace Foodpanda\ApiSdk\Entity\PaymentType;

use Foodpanda\ApiSdk\Entity\DataObject;

class PaymentType extends DataObject
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $file_name;

    /**
     * @var string
     */
    protected $payment_type_code;

    /**
     * @var string
     */
    protected $checkout_text;

    /**
     * @var bool
     */
    protected $has_need_for_change;

    /**
     * @var bool
     */
    protected $is_need_for_change_required;

    /**
     * @var bool
     */
    protected $is_hosted;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * @param string $file_name
     */
    public function setFileName($file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * @return string
     */
    public function getPaymentTypeCode()
    {
        return $this->payment_type_code;
    }

    /**
     * @param string $payment_type_code
     */
    public function setPaymentTypeCode($payment_type_code)
    {
        $this->payment_type_code = $payment_type_code;
    }

    /**
     * @return string
     */
    public function getCheckoutText()
    {
        return $this->checkout_text;
    }

    /**
     * @param string $checkout_text
     */
    public function setCheckoutText($checkout_text)
    {
        $this->checkout_text = $checkout_text;
    }

    /**
     * @return boolean
     */
    public function isHasNeedForChange()
    {
        return $this->has_need_for_change;
    }

    /**
     * @param boolean $has_need_for_change
     */
    public function setHasNeedForChange($has_need_for_change)
    {
        $this->has_need_for_change = $has_need_for_change;
    }

    /**
     * @return boolean
     */
    public function isIsNeedForChangeRequired()
    {
        return $this->is_need_for_change_required;
    }

    /**
     * @param boolean $is_need_for_change_required
     */
    public function setIsNeedForChangeRequired($is_need_for_change_required)
    {
        $this->is_need_for_change_required = $is_need_for_change_required;
    }

    /**
     * @return boolean
     */
    public function isIsHosted()
    {
        return $this->is_hosted;
    }

    /**
     * @param boolean $is_hosted
     */
    public function setIsHosted($is_hosted)
    {
        $this->is_hosted = $is_hosted;
    }
}
