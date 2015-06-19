<?php
namespace Foodpanda\Bundle\Api\HealthBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Foodpanda\Bundle\Core\HealthBundle\Services\HealthService;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\FormatterInterface;

/**
 * @Route(service="foodpanda.api.health.controller.check")
 */
class CheckController
{

    /**
     * @var HealthService
     */
    protected $healthService;

    /**
     * @var FormatterInterface
     */
    protected $formatter;

    public function __construct(HealthService $healthService, FormatterInterface $formatter)
    {
        $this->healthService = $healthService;
        $this->formatter = $formatter;
    }

    /**
     * @Method({"GET"})
     * @Route("/check",
     *  name="_foodpanda_api_health_check"
     * )
     * @return Response
     */
    public function checkAction()
    {
        $startTime = microtime(true);
        $checks = $this->healthService->checkAll();
        $endTime = microtime(true);
        return $this->formatter->format($checks, $startTime, $endTime);
    }
}
