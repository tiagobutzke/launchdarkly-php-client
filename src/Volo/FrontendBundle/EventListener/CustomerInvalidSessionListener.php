<?php
namespace Volo\FrontendBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;

class CustomerInvalidSessionListener
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param SessionInterface $session
     * @param LoggerInterface  $logger
     */
    public function __construct(SessionInterface $session, LoggerInterface $logger)
    {
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function handleInvalidSessionId(GetResponseEvent $event)
    {
        if (!$this->isEventValidForProcessing($event)) {
            return;
        }

        if ($this->session->isStarted() && empty($this->session->getId())) {
            $this->logger->info('Empty session ID, redirecting to /logout');
            $event->setResponse(new RedirectResponse('/logout'));
        }
    }

    /**
     * @param GetResponseEvent $event
     *
     * @return boolean
     */
    protected function isEventValidForProcessing(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return false;
        }

        /** @var Request $request */
        $request = $event->getRequest();
        if ($request->isXmlHttpRequest()) {
            return false;
        }

        if ($request->attributes->getBoolean('esi')) {
            return false;
        }

        if ($request->getPathInfo() === '/logout') {
            return false;
        }

        return true;
    }
}
