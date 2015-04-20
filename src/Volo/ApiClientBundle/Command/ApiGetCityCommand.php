<?php

namespace Volo\ApiClientBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Foodpanda\ApiSdk\Api\LocationApiClient;

class ApiGetCityCommand extends AbstractApiClientCommand
{
    /**
     * @return string
     */
    protected function getCommandName()
    {
        return 'api:location:city';
    }

    /**
     * @return string
     */
    protected function getCommandDescription()
    {
        $this->addArgument('city_id', InputArgument::REQUIRED);

        return 'Display a city';
    }

    /**
     * @return LocationApiClient
     */
    protected function getClientApi()
    {
        return $this->getContainer()->get('volo_frontend.api.location_api_client');
    }

    /**
     * {@inheritdoc}
     */
    protected function executeApiCall(InputInterface $input)
    {
        return $this->getClientApi()->getCity($input->getArgument('city_id'));
    }
}
