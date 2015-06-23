<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Foodpanda\Bundle\Core\HealthBundle\Model\Status;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\CheckInterface;
use Foodpanda\Bundle\Core\ReleaseInformation;

class VersionService implements CheckInterface
{
    /**
     * @var ReleaseInformation
     */
    protected $releaseInformation;

    protected $status;

    /**
     * @param ReleaseInformation $releaseInformation
     */
    public function __construct(ReleaseInformation $releaseInformation)
    {
        $this->releaseInformation = $releaseInformation;
        $this->status = new Status();
    }

    /**
     * @return bool
     */
    public function check()
    {
        $status = ($this->releaseInformation->getCommit() !== null && $this->releaseInformation->getBranch() !== null);
        if ($status === false) {
            if ($this->releaseInformation->getCommit() === null) {
                $this->status->addMessage('Commit is null');
            }
            if ($this->releaseInformation->getBranch() === null) {
                $this->status->addMessage('Branch is null');
            }
        }
        $this->status->setStatus($status);
        return $this->status;
    }
}
