<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Foodpanda\Bundle\Core\HealthBundle\Model\Status;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\CheckInterface;
use Foodpanda\Bundle\Core\ConfigurationBundle\Entity\Repository\ConfigurationFoodpandaRepository;

class DatabaseService implements CheckInterface
{
    /**
     * @var ConfigurationFoodpandaRepository
     */
    protected $configurationFoodpandaRepository;

    protected $status;

    /**
     * @param ConfigurationFoodpandaRepository $configurationFoodpandaRepository
     */
    public function __construct(ConfigurationFoodpandaRepository $configurationFoodpandaRepository)
    {
        $this->configurationFoodpandaRepository = $configurationFoodpandaRepository;
        $this->status = new Status();
    }

    /**
     * @return bool
     */
    public function check()
    {
        $foodpandaDbConfiguration = $this->configurationFoodpandaRepository->getConfiguration();
        $this->status->setStatus(!empty($foodpandaDbConfiguration));
        return $this->status;
    }
}
