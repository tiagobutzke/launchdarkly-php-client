<?php

namespace Volo\FrontendBundle\Service;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;

class PhoneNumberService
{
    /**
     * @var PhoneNumberUtil
     */
    protected $phoneNumberUtil;

    /**
     * @var string
     */
    protected $twoLetterCountryCode;

    /**
     * @param PhoneNumberUtil $phoneNumberUtil
     * @param string $twoLetterCountryCode
     */
    public function __construct(PhoneNumberUtil $phoneNumberUtil, $twoLetterCountryCode)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->twoLetterCountryCode = strtoupper(substr($twoLetterCountryCode, 0, 2));
    }

    /**
     * @param PhoneNumber $parsedPhone
     *
     * @throws PhoneNumberValidationException
     */
    public function validateNumber(PhoneNumber $parsedPhone)
    {
        if (!$this->phoneNumberUtil->isValidNumber($parsedPhone)) {
            throw new PhoneNumberValidationException('Invalid Phone Number');
        }
    }

    /**
     * @param string $phoneNumber
     *
     * @throws PhoneNumberValidationException
     *
     * @return PhoneNumber
     */
    public function parsePhoneNumber($phoneNumber)
    {
        try {
            return $this->phoneNumberUtil->parse($phoneNumber, $this->twoLetterCountryCode);
        } catch (NumberParseException $e) {
            throw new PhoneNumberValidationException($e->getMessage(), $e->getCode());
        }
    }
}
