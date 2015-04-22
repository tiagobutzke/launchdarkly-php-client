<?php

namespace Foodpanda\ApiSdk\Entity\Configuration;

use Foodpanda\ApiSdk\Entity\DataObject;

class SocialConnects extends DataObject
{
    /**
     * @var array
     */
    protected $social_connects;

    public function __construct()
    {
        $this->social_connects = [];
    }

    /**
     * @return array
     */
    public function getSocialConnects()
    {
        return $this->social_connects;
    }

    /**
     * @param array $social_connects
     */
    public function setSocialConnects(array $social_connects)
    {
        $this->social_connects = $social_connects;
    }
}
