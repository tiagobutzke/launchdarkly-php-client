<?php

namespace Foodpanda\ApiSdk\Entity\Cuisine;

use Foodpanda\ApiSdk\Entity\DataObject;

class Cuisine extends DataObject
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * string
     */
    protected $name;

    /**
     * @return mixed
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
