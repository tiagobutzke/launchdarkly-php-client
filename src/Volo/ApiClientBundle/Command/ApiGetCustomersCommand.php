<?php

namespace Volo\ApiClientBundle\Command;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Symfony\Component\Console\Input\InputOption;
use Foodpanda\ApiSdk\Api\Auth\Credentials;
use Foodpanda\ApiSdk\Api\CustomerApiClient;
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

        $data  = $this->getClientApi()->authenticate($credentials);
        $token = new AccessToken($data['access_token'], $data['token_type'], $data);

        return $this->getClientApi()->getCustomers($token);
    }
}
