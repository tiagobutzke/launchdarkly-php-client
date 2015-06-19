<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Interfaces;

interface FormatterInterface
{
    /**
     * @param array $checks
     * @param float $startTime
     * @param float $endTime
     * @return \Symfony\Component\HttpFoundation\Response;
     */
    public function format(array $checks, $startTime, $endTime);
}
