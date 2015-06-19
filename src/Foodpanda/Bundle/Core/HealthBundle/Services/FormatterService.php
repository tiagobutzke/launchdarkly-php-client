<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Foodpanda\Bundle\Core\HealthBundle\Interfaces\FormatterInterface;
use Foodpanda\Bundle\Core\ReleaseInformation;
use Symfony\Component\HttpFoundation\Response;

class FormatterService implements FormatterInterface
{


    /**
     * @var $releaseInformation;
     */
    protected $releaseInformation;

    /**
     * @param ReleaseInformation $releaseInformation
     */
    public function __construct(ReleaseInformation $releaseInformation)
    {
        $this->releaseInformation = $releaseInformation;
    }

    /**
     * @param array $checks
     * @param float $startTime
     * @param float $endTime
     * @return \Symfony\Component\HttpFoundation\Response;
     */
    public function format(array $checks, $startTime, $endTime)
    {
        $uniqueStatuses = array_unique(array_column($checks, 'status'));

        if (count($uniqueStatuses) === 1) {
            $overallStatus = reset($uniqueStatuses);
        } elseif (in_array(false, $uniqueStatuses, true) === true) {
            $overallStatus = false;
        } else {
            $overallStatus = null;
        }


        $responseArray = [
            'http-status' => ($overallStatus === false ?  Response::HTTP_SERVICE_UNAVAILABLE : Response::HTTP_OK),
            'version' => $this->releaseInformation->getBranch() . " / " . $this->releaseInformation->getCommit(),
            'time' => round($endTime - $startTime, 5),
            'status' => $overallStatus,
            'hostname' => gethostname(),
            'domain' => (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : ''),
            'checks' => $checks
        ];

        $response = new Response(
            json_encode($responseArray),
            ($overallStatus === false ?  Response::HTTP_SERVICE_UNAVAILABLE : Response::HTTP_OK),
            ['content-type' => 'application/json']
        );
        return $response;
    }
}
