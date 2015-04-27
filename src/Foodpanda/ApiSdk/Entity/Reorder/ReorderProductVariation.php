<?php

namespace Foodpanda\ApiSdk\Entity\Reorder;

use Foodpanda\ApiSdk\Entity\DataObject;

class ReorderProductVariation extends DataObject
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
     * @var int
     */
    protected $quantity;

    /**
     * @var bool
     */
    protected $is_available;

    /**
     * @var string
     */
    protected $error_message;

    /**
     * @var ReorderChoicesCollection
     */
    protected $choices;

    /**
     * @var ReorderToppingsCollection
     */
    protected $toppings;

    public function __construct()
    {
        $this->choices = new ReorderChoicesCollection();
        $this->toppings = new ReorderToppingsCollection();
    }

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

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return boolean
     */
    public function isIsAvailable()
    {
        return $this->is_available;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * @return ReorderChoicesCollection
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @return ReorderToppingsCollection
     */
    public function getToppings()
    {
        return $this->toppings;
    }
}
