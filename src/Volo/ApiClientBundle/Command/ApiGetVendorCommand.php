<?php

namespace Volo\ApiClientBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Foodpanda\ApiSdk\Api\LocationApiClient;

class ApiGetVendorCommand extends AbstractApiClientCommand
{
    /**
     * @return string
     */
    protected function getCommandName()
    {
        return 'api:vendors:vendor';
    }

    /**
     * @return string
     */
    protected function getCommandDescription()
    {
        $this->addArgument('vendor_id', InputArgument::REQUIRED);

        return 'Display a vendor';
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
        return $this->getClientApi()->getCity($input->getArgument('vendor_id'));
    }
}
