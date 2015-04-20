<?php

namespace Volo\ApiClientBundle\Command;

use Foodpanda\ApiSdk\Api\Auth\Credentials;
use Symfony\Component\Console\Input\InputOption;
use Foodpanda\ApiSdk\Api\CustomerApiClient;
use Symfony\Component\Console\Input\InputInterface;

class ApiGetTokenCommand extends AbstractApiClientCommand
{
    /**
     * @return string
     */
    protected function getCommandName()
    {
        return 'api:customers:token';
    }

    /**
     * @return string
     */
    protected function getCommandDescription()
    {
        return 'Get tokens';
    }

    /**
     * @return CustomerApiClient
     */
    protected function getClientApi()
    {
        return $this->getContainer()->get('volo_api_client.api.customer_api_client');
    }

    protected function configure()
    {
        parent::configure();

        $this->addOption('username', 'u', InputOption::VALUE_REQUIRED);
        $this->addOption('password', 'p', InputOption::VALUE_REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function executeApiCall(InputInterface $input)
    {
        $credentials = new Credentials($input->getOption('username'), $input->getOption('password'));

        return $this->getClientApi()->authenticate($credentials);
    }
}
