<?php

namespace Volo\ApiClientBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Volo\ApiClientBundle\Api\AbstractApiClient;

abstract class AbstractApiClientCommand extends ContainerAwareCommand
{
    /**
     * @return string
     */
    abstract protected function getCommandName();

    /**
     * @return string
     */
    abstract protected function getCommandDescription();

    /**
     * @param InputInterface $input
     *
     * @return array
     */
    abstract protected function executeApiCall(InputInterface $input);

    /**
     * @return AbstractApiClient
     */
    abstract protected function getClientApi();

    protected function configure()
    {
        $this
            ->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
            ->addOption('dump', 'd', InputOption::VALUE_NONE, 'var_dump output format');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $this->executeApiCall($input);

        $this->displayApiResult($input, $output, $data);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $data
     */
    protected function displayApiResult(InputInterface $input, OutputInterface $output, $data)
    {
        $input->getOption('dump') ? dump($data) : $output->writeln(json_encode($data, JSON_PRETTY_PRINT));
    }
}
