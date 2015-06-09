<?php

namespace Volo\FrontendBundle\Service;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Entity\Customer\AuthenticatedCustomer;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\Customer\CustomerPassword;
use Foodpanda\ApiSdk\Entity\Order\GuestCustomer;
use Foodpanda\ApiSdk\Provider\CustomerProvider;
use Foodpanda\ApiSdk\Serializer;
use libphonenumber\PhoneNumber;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Volo\FrontendBundle\Security\Token;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;

class CustomerService
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var CustomerProvider
     */
    protected $provider;

    /**
     * @var PhoneNumberService
     */
    protected $phoneService;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @param TokenStorage       $tokenStorage
     * @param CustomerProvider   $provider
     * @param Serializer         $serializer
     * @param PhoneNumberService $phoneService
     *
     * @internal param PhoneNumberUtil $phoneNumberUtil
     */
    public function __construct(
        TokenStorage $tokenStorage,
        CustomerProvider $provider,
        Serializer $serializer,
        PhoneNumberService $phoneService
    ) {
        $this->serializer = $serializer;
        $this->provider = $provider;
        $this->phoneService = $phoneService;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param array $customerParameters
     *
     * @throws PhoneNumberValidationException
     *
     * @return Customer
     */
    public function createCustomer(array $customerParameters)
    {
        $customer = $this->serializer->denormalizeCustomer($customerParameters);
        $validPhoneNumber = $this->validatePhoneNumber($customer->getMobileNumber());
        $this->setParsedMobileNumber($customer, $validPhoneNumber);

        return $this->provider->createCustomer($customer);
    }

    /**
     * @param CustomerPassword $customerPassword
     *
     * @return AuthenticatedCustomer
     */
    public function updateCustomerPassword(CustomerPassword $customerPassword)
    {
        /** @var Token $token */
        $token = $this->tokenStorage->getToken();

        $authenticatedCustomer = $this->provider->updatePassword($token->getAccessToken(), $customerPassword);

        $this->updateCustomerInSession($authenticatedCustomer);

        return $authenticatedCustomer;
    }

    /**
     * @param array $customerParameters
     *
     * @throws PhoneNumberValidationException
     *
     * @return AuthenticatedCustomer
     */
    public function updateCustomer(array $customerParameters)
    {
        /** @var Token $token */
        $token = $this->tokenStorage->getToken();
        $customer = $this->serializer->denormalizeCustomer($customerParameters);
        $validPhoneNumber = $this->validatePhoneNumber($customer->getMobileNumber());
        $this->setParsedMobileNumber($customer, $validPhoneNumber);

        $authenticatedCustomer = $this->provider->updateCustomer($token->getAccessToken(), $customer);

        $this->updateCustomerInSession($authenticatedCustomer);

        return $authenticatedCustomer;
    }

    /**
     * @param Customer $customer
     * @param PhoneNumber $parsedNumber
     */
    protected function setParsedMobileNumber($customer, PhoneNumber $parsedNumber)
    {
        $customer->setMobileCountryCode($parsedNumber->getCountryCode());
        $customer->setMobileNumber($parsedNumber->getNationalNumber());
    }

    /**
     * @param string $mobileNumber
     *
     * @return PhoneNumber
     */
    protected function validatePhoneNumber($mobileNumber)
    {
        $parsedNumber = $this->phoneService->parsePhoneNumber($mobileNumber);
        $this->phoneService->validateMobilePhone($parsedNumber);

        return $parsedNumber;
    }

    /**
     * @param array $customer
     * @param array $address
     *
     * @return GuestCustomer
     */
    public function createGuestCustomer(array $customer, array $address)
    {
        $guestCustomerData = [
            'customer_address' => $address,
            'customer'         => $customer
        ];

        $guestCustomer = $this->serializer->denormalizeGuestCustomer($guestCustomerData);

        return $this->provider->create($guestCustomer);
    }

    /**
     * @param $authenticatedCustomer
     */
    protected function updateCustomerInSession(AuthenticatedCustomer $authenticatedCustomer)
    {
        if ($authenticatedCustomer->getToken() === null) {
            return;
        }

        $username = sprintf('%s %s', $authenticatedCustomer->getFirstName(), $authenticatedCustomer->getLastName());
        $token    = new Token($username, ['customer' => $authenticatedCustomer], ['ROLE_CUSTOMER']);
        $token->setAttribute('tokens', new AccessToken($authenticatedCustomer->getToken(), 'bearer'));
        $this->tokenStorage->setToken($token);
    }
}
