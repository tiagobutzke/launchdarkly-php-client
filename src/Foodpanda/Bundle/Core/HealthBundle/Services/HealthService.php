<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Foodpanda\Bundle\Core\HealthBundle\Model\Status;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\CheckInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HealthService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $checks;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @param ContainerInterface $container
     * @param array $checks
     * @param string $prefix
     */
    public function __construct(ContainerInterface $container, array $checks, $prefix)
    {
        $this->container = $container;
        $this->checks = $checks;
        $this->prefix = $prefix;
    }

    /**
     * checkService Checks one service, loads it via container.
     *
     * @param string $serviceName
     * @return bool
     */
    protected function checkService($serviceName)
    {
        try {
            $service = $this->container->get($this->prefix . $serviceName);
            if (!($service instanceof CheckInterface)) {
                throw new \RuntimeException('Service does not implement CheckInterface');
            }
            $status = $service->check();
        } catch (ServiceNotFoundException $e) {
            $status = new Status();
            $status->setStatus(null);
            $status->addMessage($e->getMessage());
        } catch (\Exception $e) {
            $status = new Status();
            $status->setStatus(false);
            $status->addMessage($e->getMessage());
        }
        return $status;
    }

    /**
     * checkAll Checks all the services, based on enabled checks in configuration
     *
     * @return array
     */
    public function checkAll()
    {
        $statuses = array();
        foreach ($this->checks as $serviceName) {
            $status = $this->checkService($serviceName);
            $statuses[$serviceName] = $status->setEndTime(microtime(true))->toArray();
        }
        return $statuses;
    }
}
