<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Entity\Configuration\Configuration;
use Foodpanda\ApiSdk\Provider\ConfigurationProvider;

class ConfigurationService
{
    /**
     * @var ConfigurationProvider
     */
    protected $configurationProvider;

    private $configurationInstance;

    /**
     * @param ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * Get the cached Configuration object
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        if (null === $this->configurationInstance) {
            $this->configurationInstance = $this->configurationProvider->findAll();
        }

        return $this->configurationInstance;
    }
}
