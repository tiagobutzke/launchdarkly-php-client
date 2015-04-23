<?php

namespace Foodpanda\ApiSdk\Entity\OAuth;

use Foodpanda\ApiSdk\Entity\DataObject;

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
     * @return string
     */
    public function getOAuthToken()
    {
        return $this->o_auth_token;
    }

    /**
     * @return string
     */
    public function getOAuthTokenSecret()
    {
        return $this->o_auth_token_secret;
    }
}
