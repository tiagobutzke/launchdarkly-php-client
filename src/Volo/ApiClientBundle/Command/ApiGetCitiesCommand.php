<?php

namespace Volo\ApiClientBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Foodpanda\ApiSdk\Api\LocationApiClient;

class ApiGetCitiesCommand extends AbstractApiClientCommand
{
    /**
     * @return string
     */
    protected function getCommandName()
    {
        return 'api:location:cities';
    }

    /**
     * @return string
     */
    protected function getCommandDescription()
    {
        return 'Display a list of cities';
    }

    /**
     * @return LocationApiClient
     */
    protected function getClientApi()
    {
        return $this->getContainer()->get('volo_api_client.api.location_api_client');
    }

    /**
     * {@inheritdoc}
     */
    protected function executeApiCall(InputInterface $input)
    {
        return $this->getClientApi()->getCities();
    }
}
