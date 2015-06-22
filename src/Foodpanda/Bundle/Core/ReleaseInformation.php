<?php
namespace Foodpanda\Bundle\Core;

/**
 * Class ReleaseInformation
 *
 * @package Foodpanda\Bundle\Core\ConfigurationBundle\Services
 */
class ReleaseInformation
{

    /**
     * @var string
     */
    protected $branch;

    /**
     * @var string
     */
    protected $commit;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @param string $branch
     * @param string $commit
     * @param string $environment
     */
    public function __construct(
        $branch,
        $commit,
        $environment
    ) {
        $this->branch      = $branch;
        $this->commit      = $commit;
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @return string
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Return the release information as a pipe ("|") separated string
     *
     * @return string
     */
    public function getTrackingAppVersion()
    {
        return implode(
            '|',
            [
                $this->getEnvironment(),
                $this->getBranch(),
                $this->getCommit(),
            ]
        );
    }
}
