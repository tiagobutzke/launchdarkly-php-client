<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Interfaces;

interface CheckInterface
{
    /**
     * @return Foodpanda\Bundle\Core\HealthBundle\Model\Status;
     */
    public function check();
}
