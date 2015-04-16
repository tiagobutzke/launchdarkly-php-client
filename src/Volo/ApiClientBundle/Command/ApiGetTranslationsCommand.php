<?php

namespace Volo\ApiClientBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ApiGetTranslationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('api:get:translations')
            ->setDescription('Display a list of translations')
            ->addOption('dump', 'd', InputOption::VALUE_NONE, 'var_dump output format');
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiClient = $this->getContainer()->get('volo_api_client.client');

        $data = $apiClient->getTranslations();

        $input->getOption('dump') ? dump($data) : $output->writeln(json_encode($data, JSON_PRETTY_PRINT));
    }
}