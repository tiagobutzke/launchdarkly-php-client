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
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
