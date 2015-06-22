<?php

namespace Volo\FrontendBundle\Service;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Translation\TranslatorInterface;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;

class PhoneNumberService
{
    const CODE_GENERAL_EXCEPTION = 1001;
    /**
     * @var PhoneNumberUtil
     */
    protected $phoneNumberUtil;

    /**
     * @var string
     */
    protected $twoLetterCountryCode;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param PhoneNumberUtil     $phoneNumberUtil
     * @param string              $twoLetterCountryCode
     * @param TranslatorInterface $translator
     */
    public function __construct(
        PhoneNumberUtil $phoneNumberUtil,
        $twoLetterCountryCode,
        TranslatorInterface $translator
    ) {
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->twoLetterCountryCode = strtoupper(substr($twoLetterCountryCode, 0, 2));
        $this->translator = $translator;
    }

    /**
     * @param PhoneNumber $parsedPhone
     *
     * @throws PhoneNumberValidationException
     */
    public function validateNumber(PhoneNumber $parsedPhone)
    {
        if (!$this->phoneNumberUtil->isValidNumber($parsedPhone)) {
            throw new PhoneNumberValidationException(
                $this->getExceptionMessage(static::CODE_GENERAL_EXCEPTION),
                static::CODE_GENERAL_EXCEPTION
            );
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
            throw new PhoneNumberValidationException($this->getExceptionMessage($e->getCode()), $e->getCode());
        }
    }

    /**
     * @param int $code
     *
     * @return string
     */
    protected function getExceptionMessage($code)
    {
        switch ($code) {
            case NumberParseException::INVALID_COUNTRY_CODE:
                $message = 'error.phone_number.invalid_country_code';
                break;
            case NumberParseException::NOT_A_NUMBER:
                $message = 'error.phone_number.not_a_number';
                break;
            case NumberParseException::TOO_SHORT_AFTER_IDD:
            case NumberParseException::TOO_SHORT_NSN:
                $message = 'error.phone_number.too_short';
                break;
            case NumberParseException::TOO_LONG:
                $message = 'error.phone_number.too_long';
                break;
            case static::CODE_GENERAL_EXCEPTION:
            default:
                $message = 'error.phone_number.invalid';
                break;
        }

        return $this->translator->trans($message);
    }
}
