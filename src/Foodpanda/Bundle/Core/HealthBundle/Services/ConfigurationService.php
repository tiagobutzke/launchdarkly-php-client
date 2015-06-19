<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Foodpanda\Bundle\Core\HealthBundle\Model\Status;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\CheckInterface;

class ConfigurationService implements CheckInterface
{
    /**
     * @var Status
     */
    protected $status;

    public function __construct()
    {
        $this->status = new Status();
    }

    /**
     * Dont know what to check.
     * Seems symfony2 will not start if parts of config is missing.
     * @return bool
     */
    public function check()
    {
        $this->status->setStatus(true);
        return $this->status;
    }
}
