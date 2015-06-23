<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Foodpanda\Bundle\Core\HealthBundle\Model\Status;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\CheckInterface;
use Aws\Common\Exception\InstanceProfileCredentialsException;
use Foodpanda\Bundle\Core\QueueBundle\Services\QueueClientSqs;
use Guzzle\Service\Resource\Model as AwsResponseModel;

class QueueService implements CheckInterface
{
    const QUEUE_MESSAGES_KEY = 'ApproximateNumberOfMessages';
    const QUEUE_MESSAGES_NOT_VISIBLE_KEY = 'ApproximateNumberOfMessagesNotVisible';
    const QUEUE_MESSAGES_DELAYED_KEY = 'ApproximateNumberOfMessagesDelayed';

    /**
     * @var QueueClientSqs
     */
    protected $queueClient;

    /**
     * @var string
     */
    protected $sqsEndPoint;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @param QueueClientSqs $queueClient
     */
    public function __construct(QueueClientSqs $queueClient)
    {
        $this->queueClient = $queueClient;
        $this->status = new Status();
    }

    /**
     * @return bool
     */
    public function check()
    {
        try {
            $response = $this->queueClient->getQueueAttributes();

            $this->status->addMessage(
                'Messages in queue: ' . $response[static::QUEUE_MESSAGES_KEY]
            );
            $this->status->addMessage(
                'Messages in queue (not visible): ' . $response[static::QUEUE_MESSAGES_NOT_VISIBLE_KEY]
            );
            $this->status->addMessage(
                'Messages in queue (delayed): ' . $response[static::QUEUE_MESSAGES_DELAYED_KEY]
            );
            $this->status->setStatus(true);
        } catch (InstanceProfileCredentialsException $e) {
            $this->status->addMessage($e->getMessage());
            $this->status->setStatus(false);
        }

        return $this->status;
    }
}
