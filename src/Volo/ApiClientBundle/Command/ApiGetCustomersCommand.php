<?php

namespace Volo\ApiClientBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Volo\ApiClientBundle\Api\Auth\Credentials;
use Volo\ApiClientBundle\Api\CustomerApiClient;
use Symfony\Component\Console\Input\InputInterface;

class ApiGetCustomersCommand extends AbstractApiClientCommand
{
    /**
     * @return string
     */
    protected function getCommandName()
    {
        return 'api:customers:customers';
    }

    /**
     * @return string
     */
    protected function getCommandDescription()
    {
        return 'Display the current customer';
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

        $token = $this->getClientApi()->authenticate($credentials);

        return $this->getClientApi()->getCustomers($token);
    }
}