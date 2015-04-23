<?php

namespace Foodpanda\ApiSdk\Entity\Customer;

use Foodpanda\ApiSdk\Entity\DataObject;

class ForgotPassword extends DataObject
{
    /**
     * @var string
     */
    protected $email_status;

    /**
     * @var string
     */
    protected $customer_status;

    /**
     * @var string
     */
    protected $message;

    /**
     * @return string
     */
    public function getEmailStatus()
    {
        return $this->email_status;
    }

    /**
     * @return string
     */
    public function getCustomerStatus()
    {
        return $this->customer_status;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
