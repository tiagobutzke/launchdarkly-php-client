<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Foodpanda\Bundle\Core\HealthBundle\Model\Status;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\CheckInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionService implements CheckInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    protected $status;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->status = new Status();
    }

    /**
     * @return bool
     */
    public function check()
    {
        $sessionValue = uniqid('checkSession_', true);
        $this->session->set($sessionValue, $sessionValue);
        $this->session->save();
        $this->session->start();
        $storedValue = $this->session->get($sessionValue, null);
        $this->status->setStatus($storedValue === $sessionValue);
        return $this->status;
    }
}
