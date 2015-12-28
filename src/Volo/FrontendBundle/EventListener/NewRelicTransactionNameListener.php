<?php

namespace Volo\FrontendBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Sets proper NewRelic transaction name on every request.
 * Otherwise we have issues with sub requests which override transaction names.
 */
class NewRelicTransactionNameListener
{
    /**
     * @param FilterControllerEvent $event
     */
    public function setTransactionName(FilterControllerEvent $event)
    {
        $isMasterRequest = $event->getRequestType() === HttpKernelInterface::MASTER_REQUEST;
        if (function_exists("newrelic_name_transaction") && $isMasterRequest) {
            $basicNewRelicTransactionName = $event->getRequest()->attributes->get('_controller');

            newrelic_name_transaction($basicNewRelicTransactionName);
        }
    }
}
