<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use K3ssen\GeneratorBundle\Command\Style\CommandStyle;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatatableCommand extends CrudCommand
{
    protected const TITLE = 'Generate Datatable';

    protected static $defaultName = 'generator:datatable';

    protected function configure()
    {
        $this->setDescription('Generate datatable for existing entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if ($this->generateOptions->getCheckSgDatatablesBundleEnabled() && !$this->bundleProvider->isEnabled('SecurityBundle')) {
            $io = new CommandStyle($input, $output);
            $io->warning('SecurityBundle is not enabled.');
            if (!$io->confirm('Do you still want to proceed?')) {
                return;
            }
        }
        parent::execute($input, $output);
    }

    protected function generateFiles(MetaEntityInterface $metaEntity): array
    {
        return $this->crudGenerator->createDatatable($metaEntity);
    }

    protected function askQuestions(InputInterface $input, OutputInterface $output): void
    {
        return;
    }
}
