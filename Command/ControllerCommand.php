<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ControllerCommand extends CrudCommand
{
    protected const TITLE = 'Generate Controller';

    protected static $defaultName = 'generator:controller';

    protected function configure()
    {
        $this->setDescription('Generate controller for existing entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('controller-subdirectory', null,InputOption::VALUE_OPTIONAL, 'Subdirectory for controller')
            ->addOption('use-voter', null,InputOption::VALUE_OPTIONAL)
            ->addOption('use-datatable', null,InputOption::VALUE_OPTIONAL)
            ->addOption('use-write-actions', null,InputOption::VALUE_OPTIONAL)
        ;
    }

    protected function generateFiles(MetaEntityInterface $metaEntity): array
    {
        return $this->crudGenerator->createController($metaEntity);
    }

    protected function askQuestions(InputInterface $input, OutputInterface $output): void
    {
        $this->determineControllerSubDirectory($input, $output);
        $this->determineUseWriteActions($input, $output);
        $this->determineUseVoter($input, $output);
        $this->determineUseDatatable($input, $output);
    }
}
