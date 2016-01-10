<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Foodpanda\Bundle\Core\HealthBundle\Model\Status;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\CheckInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionService implements CheckInterface
{
    use StatsTrait;

    /**
     * @var SessionInterface
     */
    protected $session;

    protected $status;

    protected $connectOptions;

    /**
     * @param SessionInterface $session
     * @param array $connectOptions
     */
    public function __construct(SessionInterface $session, array $connectOptions)
    {
        $this->session = $session;
        $this->status = new Status();
        $this->connectOptions = $connectOptions;
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
        $redis = new \Redis();
        $redis->connect($this->connectOptions['host'], $this->connectOptions['port'], $this->connectOptions['timeout']);
        foreach ($this->doGetStats($redis->info()) as $k => $v) {
            $this->status->addMessage($k . ': ' . $v);
        }

        return $this->status;
    }
}
