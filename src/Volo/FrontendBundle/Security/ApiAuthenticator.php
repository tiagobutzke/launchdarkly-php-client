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
     * @param CustomerProvider $customerProvider
     * @param Authenticator $authenticator
     */
    public function __construct(CustomerProvider $customerProvider, Authenticator $authenticator)
    {
        $this->customerProvider = $customerProvider;
        $this->authenticator = $authenticator;
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

        try {
            $accessToken = $this->authenticator->authenticate($credentials);
            $customer = $this->customerProvider->getCustomer($accessToken);
        } catch (ApiException $e) {
            throw new AuthenticationException('Invalid username or password');
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
