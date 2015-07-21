<?php

namespace Volo\FrontendBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ComingSoonListener
{
    /**
     * @var bool
     */
    private $isMaintenanceEnabled;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @param EngineInterface $templateEngine
     * @param bool $isMaintenanceEnabled
     */
    public function __construct(EngineInterface $templateEngine, $isMaintenanceEnabled)
    {
        $this->templateEngine = $templateEngine;
        $this->isMaintenanceEnabled = $isMaintenanceEnabled;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->isMaintenanceEnabled) {
            $response = $this->templateEngine->renderResponse('VoloFrontendBundle:SplashScreen:coming_soon.html.twig');

            $event->setResponse($response);
            $event->stopPropagation();
        }
    }
}
