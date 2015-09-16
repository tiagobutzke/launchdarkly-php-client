<?php

namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Foodpanda\ApiSdk\Provider\ConfigurationProvider;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\CheckInterface;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\Foodpanda;
use Foodpanda\Bundle\Core\HealthBundle\Model\Status;

class
ApiService implements CheckInterface
{
    /**
     * @var ConfigurationProvider
     */
    private $configurationProvider;

    /**
     * @var Status
     */
    private $status;

    /**
     * @param ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
        $this->status = new Status();
    }


    /**
     * @return Status
     */
    public function check()
    {
        try {
            $this->configurationProvider->findAll();
            $this->status->setStatus(true);
        } catch(\RuntimeException $e) {
            $this->status->setStatus(false);
        }

        return $this->status;
    }
}
