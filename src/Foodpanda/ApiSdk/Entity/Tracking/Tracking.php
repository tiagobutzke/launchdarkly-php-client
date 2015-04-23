<?php

namespace Foodpanda\ApiSdk\Entity\Tracking;

use Foodpanda\ApiSdk\Entity\DataObject;

class Tracking extends DataObject
{
    /**
     * @var int
     */
    protected $criterio_id;

    /**
     * @return int
     */
    public function getCriterioId()
    {
        return $this->criterio_id;
    }
}
