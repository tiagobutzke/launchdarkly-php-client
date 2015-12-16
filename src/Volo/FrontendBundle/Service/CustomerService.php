<?php

namespace Volo\FrontendBundle\Service;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Entity\Address\Address;
use Foodpanda\ApiSdk\Entity\Address\AddressesCollection;
use Foodpanda\ApiSdk\Entity\Customer\AuthenticatedCustomer;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\Customer\CustomerPassword;
use Foodpanda\ApiSdk\Entity\Order\GuestCustomer;
use Foodpanda\ApiSdk\Provider\CustomerProvider;
use Foodpanda\ApiSdk\Serializer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Volo\FrontendBundle\Security\Token;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;
use Volo\FrontendBundle\ValidPhoneNumber;

class CustomerService
{
    const SESSION_CONTACT_KEY_TEMPLATE = 'customer-contact';

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
     * @param AccessToken $accessToken
     *
     * @return Customer
     */
    public function getCustomer(AccessToken $accessToken) {
        return $this->provider->getCustomer($accessToken);
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

        $mobileNumber = $customer->getMobileNumber();
        if ($customer->getMobileCountryCode()) {
            $mobileNumber = sprintf('+%s%s', $customer->getMobileCountryCode(), $customer->getMobileNumber());
        }

        $validPhoneNumber = $this->validatePhoneNumber($mobileNumber);
        $this->setParsedMobileNumber($customer, $validPhoneNumber);

        $authenticatedCustomer = $this->provider->updateCustomer($token->getAccessToken(), $customer);

        $this->updateCustomerInSession($authenticatedCustomer);

        return $authenticatedCustomer;
    }

    /**
     * @param Customer $customer
     * @param ValidPhoneNumber $parsedNumber
     */
    protected function setParsedMobileNumber($customer, ValidPhoneNumber $parsedNumber)
    {
        $customer->setMobileCountryCode($parsedNumber->getCountryCode());
        $customer->setMobileNumber($parsedNumber->getNationalNumber());
    }

    /**
     * @param string $mobileNumber
     *
     * @return ValidPhoneNumber
     */
    public function validatePhoneNumber($mobileNumber)
    {
        $parsedNumber = $this->phoneService->parsePhoneNumber($mobileNumber);
        $this->phoneService->validateNumber($parsedNumber);

        return new ValidPhoneNumber($parsedNumber);
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
        $accessToken = $authenticatedCustomer->getToken();

        if ($accessToken === null) {
            return;
        }

        // Backwards compatibility, in API 2.62 the token field contains an accessToken object
        if (is_array($accessToken)) {
            $accessToken = $accessToken['access_token'];
        }

        $username = sprintf('%s %s', $authenticatedCustomer->getFirstName(), $authenticatedCustomer->getLastName());
        $token    = new Token($username, ['customer' => $authenticatedCustomer], ['ROLE_CUSTOMER']);
        $token->setAttribute('tokens', new AccessToken($accessToken, 'bearer'));
        $this->tokenStorage->setToken($token);
    }

    /**
     * @param int $id
     * @param AccessToken $accessToken
     * @param int $vendorId
     *
     * @return AddressesCollection
     */
    public function getAddress($id, AccessToken $accessToken, $vendorId = null)
    {
        return $this->provider->getAddress($id, $accessToken, $vendorId);
    }

    /**
     * @param AccessToken $accessToken
     * @param int $vendorId
     *
     * @return AddressesCollection
     */
    public function getAddresses(AccessToken $accessToken, $vendorId = null)
    {
        return $this->provider->getAddresses($accessToken, $vendorId)->getItems();
    }

    /**
     * @param Address $address
     * @param AccessToken $accessToken
     *
     * @return Address
     */
    public function create(Address $address, AccessToken $accessToken)
    {
        return $this->provider->createAddress($accessToken, $address);
    }

    /**
     * @param array $data
     * @param AccessToken $accessToken
     */
    public function saveCustomerAddressFromGuestCustomer(array $data, AccessToken $accessToken)
    {
        $customerAddresses = $this->getAddresses($accessToken);

        /** @var Address $address */
        $address = $this->serializer->denormalizeCustomerAddress($data);

        if ($customerAddresses->isAlreadySaved($address)) {
            $address = $customerAddresses->findSimilar($address);
            $this->updateAddress($address, $accessToken);
        } else {
            $this->create($address, $accessToken);
        }
    }

    /**
     * @param Address $address
     * @param AccessToken $accessToken
     *
     * @return Address
     */
    public function updateAddress(Address $address, AccessToken $accessToken)
    {
        return $this->provider->updateAddress($address, $accessToken);
    }

    /**
     * @param int $id
     * @param AccessToken $accessToken
     */
    public function deleteAddress($id, AccessToken $accessToken)
    {
        $this->provider->deleteAddress($id, $accessToken);
    }
}
