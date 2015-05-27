<?php

namespace Volo\FrontendBundle\Security;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Role\RoleInterface;

class Token extends AbstractToken
{
    /**
     * Constructor.
     *
     * @param string|object            $user  The username (like a nickname, email address, etc.), or a UserInterface instance or an object implementing a __toString method.
     * @param array                    $attributes
     * @param RoleInterface[]|string[] $roles An array of roles
     */
    public function __construct($user, array $attributes = array(), array $roles = array())
    {
        parent::__construct($roles);

        $this->setUser($user);
        $this->setAttributes($attributes);

        parent::setAuthenticated(count($roles) > 0);
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken()
    {
        return $this->getAttribute('tokens');
    }

    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return $this->getUsername();
    }
}
