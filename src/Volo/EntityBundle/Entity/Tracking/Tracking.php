<?php

namespace Volo\EntityBundle\Entity\Tracking;

use Volo\EntityBundle\Entity\DataObject;

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

    /**
     * @param int $criterio_id
     */
    public function setCriterioId($criterio_id)
    {
        $this->criterio_id = $criterio_id;
    }
}
