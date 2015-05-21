<?php

namespace Volo\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class DumpThumborConfigurationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('volo:thumbor:dump')
            ->setDescription('Dump thumbor configuration JS file')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $content = $this->getContainer()->get('volo_frontend.thumbor_dumper')->dump();

        $filesystem = new Filesystem();
        $targetFolder = sprintf('%s/%s', $this->getContainer()->getParameter('kernel.root_dir'), '../web/thumbor');
        $filename = sprintf('%s/%s', $targetFolder, 'configuration.js');

        $output->writeln(sprintf('<info>[file+]</info> %s', $filename));
        $filesystem->dumpFile($filename, $content);
    }
}
