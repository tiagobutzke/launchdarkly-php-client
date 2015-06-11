<?php

namespace Volo\FrontendBundle\Twig;

use Volo\FrontendBundle\OrderStatus;

class OrderExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array_merge(parent::getFilters(), [
            new \Twig_SimpleFilter('activateStepTracking', [$this, 'activateStepTracking']),
        ]);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'volo_frontend.order_extension';
    }

    /**
     * @param string $cssClassName
     * @param int $position
     * @param array $status
     *
     * @return string
     */
    public function activateStepTracking($cssClassName, $position, array $status)
    {
        if (count($status['status_history']) === 0) {
            return '';
        }

        $code           = $status['status_history'][0]['code'];
        $changedAt      = $status['status_history'][0]['changedAt'];
        $availableSteps = OrderStatus::$userCodeMapping[$code];

        /**
         * Temporary workaround to fake "Order being prepared" status
         */
        if (11 === $code) { // 11 = Order Accepted
            $now = new \DateTime('now', new \DateTimeZone($changedAt['timezone']));
            $lastStatusUpdate = \DateTime::createFromFormat(
                'Y-m-d H:i:s.u',
                $changedAt['date'],
                new \DateTimeZone($changedAt['timezone'])
            );
            if ($lastStatusUpdate) {
                $lastStatusUpdate->add(\DateInterval::createFromDateString('2 minute'));
                if ($now >= $lastStatusUpdate) {
                    $availableSteps = OrderStatus::$userCodeMapping[0];
                }
            }
        }

        if (in_array($position, $availableSteps)) {
            return $cssClassName;
        }

        return '';
    }
}
