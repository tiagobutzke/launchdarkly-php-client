<?php

namespace Volo\FrontendBundle\Security;

use Foodpanda\ApiSdk\Api\Auth\Credentials;
use Foodpanda\ApiSdk\Api\Authenticator;
use Foodpanda\ApiSdk\Exception\ApiException;
use Foodpanda\ApiSdk\Provider\CustomerProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ApiAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * @var CustomerProvider
     */
    private $customerProvider;

    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param CustomerProvider    $customerProvider
     * @param Authenticator       $authenticator
     * @param TranslatorInterface $translator
     */
    public function __construct(
        CustomerProvider $customerProvider,
        Authenticator $authenticator,
        TranslatorInterface $translator
    ) {
        $this->customerProvider = $customerProvider;
        $this->authenticator = $authenticator;
        $this->translator = $translator;
    }

    /**
     * @param TokenInterface        $token
     * @param UserProviderInterface $userProvider
     * @param string                $providerKey
     *
     * @return Token
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $credentials = new Credentials($token->getUsername(), $token->getCredentials());

        return $this->login($credentials);
    }

    /**
     * @param Credentials $credentials
     *
     * @return Token
     */
    public function login(Credentials $credentials)
    {
        try {
            $accessToken = $this->authenticator->authenticate($credentials);
            $customer = $this->customerProvider->getCustomer($accessToken);
        } catch (ApiException $e) {
            throw new AuthenticationException($this->translator->trans('error.invalid_username_or_password'));
        }

        $username = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        $token    = new Token($username, ['customer' => $customer], ['ROLE_CUSTOMER']);
        $token->setAttribute('tokens', $accessToken);

        return $token;
    }

    /**
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken
        && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param Request $request
     * @param string  $username
     * @param string  $password
     * @param string  $providerKey
     *
     * @return UsernamePasswordToken
     */
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }
}
