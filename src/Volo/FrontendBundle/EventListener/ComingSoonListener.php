<?php

namespace Volo\FrontendBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ComingSoonListener
{
    private $whitelistedRoutes = [
        '_coming-soon-disable',
        '_foodpanda_api_health_check'
    ];

    /**
     * @var bool
     */
    private $isMaintenanceEnabled;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param EngineInterface $templateEngine
     * @param Router $router
     * @param bool $isMaintenanceEnabled
     */
    public function __construct(EngineInterface $templateEngine, Router $router, $isMaintenanceEnabled)
    {
        $this->templateEngine = $templateEngine;
        $this->router = $router;
        $this->isMaintenanceEnabled = $isMaintenanceEnabled;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($this->isMaintenanceEnabled && !$request->cookies->has('coming-soon-disabled')) {
            try {
                $match = $this->router->match($request->getPathInfo());
                if (isset($match['_route']) && in_array($match['_route'], $this->whitelistedRoutes)) {
                    return;
                }
            } catch(MethodNotAllowedException $e) {

            } catch(ResourceNotFoundException $e) {

            }
            $response = $this->templateEngine->renderResponse('VoloFrontendBundle:SplashScreen:coming_soon.html.twig');

            $event->setResponse($response);
            $event->stopPropagation();
        }
    }
}
