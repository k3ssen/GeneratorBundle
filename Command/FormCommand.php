<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FormCommand extends CrudCommand
{
    protected const TITLE = 'Generate Form';

    protected static $defaultName = 'generator:form';

    protected function configure()
    {
        $this->setDescription('Generate form for existing entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Argument description')
        ;
    }

    protected function generateFiles(MetaEntityInterface $metaEntity): array
    {
        return $this->crudGenerator->createForm($metaEntity);
    }

    protected function askQuestions(InputInterface $input, OutputInterface $output): void
    {
        return;
    }
}
