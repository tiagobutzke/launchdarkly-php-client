<?php

namespace Volo\ApiClientBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Volo\ApiClientBundle\Api\ConfigurationApiClient;

class ApiConfigurationCommand extends AbstractApiClientCommand
{
    /**
     * @return string
     */
    protected function getCommandName()
    {
        return 'api:configuration:configuration';
    }

    /**
     * @return string
     */
    protected function getCommandDescription()
    {
        return 'Display the configuration';
    }

    /**
     * @return ConfigurationApiClient
     */
    protected function getClientApi()
    {
        return $this->getContainer()->get('volo_api_client.api.configuration_api_client');
    }

    /**
     * {@inheritdoc}
     */
    protected function executeApiCall(InputInterface $input)
    {
        return $this->getClientApi()->getConfiguration();
    }
}
