<?php

namespace Volo\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class SyncTranslationsCommand extends ContainerAwareCommand implements ContainerAwareInterface
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('foodora:translations:sync')
            ->setDescription('Sync translations with WebTranslateIt API');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $catalogues = $this->getContainer()->get('volo_frontend.service.wti')->sync();
        foreach ($catalogues as $catalogue) {
            $output->writeln(sprintf('Synchronized locale: <info>%s</info>', $catalogue->getLocale()));
        }
    }
}
