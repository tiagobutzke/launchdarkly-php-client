<?php

namespace Volo\ApiClientBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApiGetCitiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('api:get:cities')
            ->setDescription('Display a list of vendors');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiClient = $this->getContainer()->get('volo_api_client.client');

        $vendors = $apiClient->getCities();
        dump($vendors);
    }
}