<?php

namespace Foodpanda\ApiSdk\Entity\Cart;

class AreaLocation extends AbstractLocation
{
    /**
     * @var int
     */
    protected $area_id;

    /**
     * @return int
     */
    public function getAreaId()
    {
        return $this->area_id;
    }

    /**
     * @param int $areaId
     */
    public function setAreaId($areaId)
    {
        $this->area_id = $areaId;
    }

    /**
     * {@inheritdoc}
     */
    protected function initLocationType()
    {
        return 'area';
    }
}
