<?php

namespace Volo\ApiClientBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Foodpanda\ApiSdk\Api\LocationApiClient;

class ApiGetVendorsCommand extends AbstractApiClientCommand
{
    /**
     * @return string
     */
    protected function getCommandName()
    {
        return 'api:vendors:vendors';
    }

    /**
     * @return string
     */
    protected function getCommandDescription()
    {
        return 'Display a list of vendors';
    }

    /**
     * @return LocationApiClient
     */
    protected function getClientApi()
    {
        return $this->getContainer()->get('volo_frontend.api.vendor_api_client');
    }

    /**
     * {@inheritdoc}
     */
    protected function executeApiCall(InputInterface $input)
    {
        return $this->getClientApi()->getCities();
    }
}
