<?php

namespace Foodpanda\ApiSdk\Entity\Choice;

use Foodpanda\ApiSdk\Entity\DataObject;

class Choice extends DataObject
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
