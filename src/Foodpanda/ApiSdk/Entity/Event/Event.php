<?php

namespace Foodpanda\ApiSdk\Entity\Event;

use Foodpanda\ApiSdk\Entity\DataObject;

class Event extends DataObject
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
