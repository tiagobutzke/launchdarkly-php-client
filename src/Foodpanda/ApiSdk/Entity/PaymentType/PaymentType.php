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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * @return string
     */
    public function getPaymentTypeCode()
    {
        return $this->payment_type_code;
    }

    /**
     * @return string
     */
    public function getCheckoutText()
    {
        return $this->checkout_text;
    }

    /**
     * @return boolean
     */
    public function isHasNeedForChange()
    {
        return $this->has_need_for_change;
    }

    /**
     * @return boolean
     */
    public function isIsNeedForChangeRequired()
    {
        return $this->is_need_for_change_required;
    }

    /**
     * @return boolean
     */
    public function isIsHosted()
    {
        return $this->is_hosted;
    }
}
