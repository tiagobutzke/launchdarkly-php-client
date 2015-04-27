<?php

namespace Foodpanda\ApiSdk\Entity\Reorder;

use Foodpanda\ApiSdk\Entity\Topping\Topping;

class ReorderTopping extends Topping
{
    /**
     * @var int
     */
    protected $position;

    /**
     * @var bool
     */
    protected $is_available;

    /**
     * @var string
     */
    protected $error_message;

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
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
}
