<?php

namespace Volo\EntityBundle\Entity\OAuth;

use Volo\EntityBundle\Entity\DataObject;

class OAuth extends DataObject
{
    /**
     * @var string
     */
    protected $expires_at;

    /**
     * @var string
     */
    protected $o_auth_token;

    /**
     * @var string
     */
    protected $o_auth_token_secret;

    /**
     * @return string
     */
    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * @param string $expires_at
     */
    public function setExpiresAt($expires_at)
    {
        $this->expires_at = $expires_at;
    }

    /**
     * @return string
     */
    public function getOAuthToken()
    {
        return $this->o_auth_token;
    }

    /**
     * @param string $o_auth_token
     */
    public function setOAuthToken($o_auth_token)
    {
        $this->o_auth_token = $o_auth_token;
    }

    /**
     * @return string
     */
    public function getOAuthTokenSecret()
    {
        return $this->o_auth_token_secret;
    }

    /**
     * @param string $o_auth_token_secret
     */
    public function setOAuthTokenSecret($o_auth_token_secret)
    {
        $this->o_auth_token_secret = $o_auth_token_secret;
    }
}
