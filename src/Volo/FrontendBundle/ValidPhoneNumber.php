<?php

namespace Volo\FrontendBundle;

use libphonenumber\PhoneNumber;

class ValidPhoneNumber
{

    /**
     * @var null|string
     */
    private $nationalNumber;

    /**
     * @var int|null
     */
    private $countryCode;

    /**
     * @param PhoneNumber $phoneNumber
     */
    public function __construct(PhoneNumber $phoneNumber)
    {
        $mobileNumber = $phoneNumber->getNationalNumber();
        if ($phoneNumber->isItalianLeadingZero()) {
            $mobileNumber = '0' . $mobileNumber;
        }

        $this->countryCode = $phoneNumber->getCountryCode();
        $this->nationalNumber = $mobileNumber;
    }

    /**
     * @return null|string
     */
    public function getNationalNumber()
    {
        return $this->nationalNumber;
    }

    /**
     * @return int|null
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }
}
