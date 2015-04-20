<?php

namespace Volo\EntityBundle\Entity\Customer;

use Volo\EntityBundle\Entity\DataObject;

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
     * @param string $email_status
     */
    public function setEmailStatus($email_status)
    {
        $this->email_status = $email_status;
    }

    /**
     * @return string
     */
    public function getCustomerStatus()
    {
        return $this->customer_status;
    }

    /**
     * @param string $customer_status
     */
    public function setCustomerStatus($customer_status)
    {
        $this->customer_status = $customer_status;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}
