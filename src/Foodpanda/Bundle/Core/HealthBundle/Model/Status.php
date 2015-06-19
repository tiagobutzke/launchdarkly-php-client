<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Model;

class Status
{
    /**
     * @var bool
     */
    protected $status;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var float
     */
    protected $startTime;

    /**
     * @var float
     */
    protected $endTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * @param float $endTime
     * @return $this
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function addMessage($message)
    {
        $this->messages[] = $message;
        return $this;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'status' => $this->status,
            'messages' => $this->messages,
            'time' => round(($this->endTime - $this->startTime), 5)
        ];
    }
}
