<?php

namespace Volo\FrontendBundle\Security;

use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Volo\ApiClientBundle\Api\Auth\Credentials;
use Volo\ApiClientBundle\Api\CustomerApiClient;

class ApiAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * @var CustomerApiClient
     */
    private $customerApiClient;

    /**
     * @param CustomerApiClient $customerApiClient
     */
    public function __construct(CustomerApiClient $customerApiClient)
    {
        $this->customerApiClient = $customerApiClient;
    }

    /**
     * @param TokenInterface        $token
     * @param UserProviderInterface $userProvider
     * @param string                $providerKey
     *
     * @return UsernamePasswordToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $credentials = new Credentials($token->getUsername(), $token->getCredentials());

        try {
            $tokens = $this->customerApiClient->authenticate($credentials);
            $user   = $this->customerApiClient->getCustomers($tokens);
        } catch (ClientException $e) {
            throw new AuthenticationException('Invalid username or password');
        }

        return new UsernamePasswordToken(
            $user['email'],
            [
                'user'  => $user,
                'token' => $tokens,
            ],
            $providerKey,
            ['ROLE_CUSTOMER']
        );
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
