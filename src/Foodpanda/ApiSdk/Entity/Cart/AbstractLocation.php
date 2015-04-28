<?php

namespace Foodpanda\ApiSdk\Entity\Cart;

use Foodpanda\ApiSdk\Entity\DataObject;

abstract class AbstractLocation extends DataObject
{
    public function __construct()
    {
        $this->location_type = $this->initLocationType();
    }

    /**
     * @var string
     */
    protected $location_type;

    /**
     * @return string
     */
    abstract protected function initLocationType();
}
