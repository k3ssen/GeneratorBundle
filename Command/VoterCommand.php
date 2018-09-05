<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use K3ssen\GeneratorBundle\Command\Style\CommandStyle;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VoterCommand extends CrudCommand
{
    protected const TITLE = 'Generate Voter';

    protected static $defaultName = 'generator:voter';

    protected function configure()
    {
        $this->setDescription('Generate voter for existing entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if (!$this->bundleProvider->isEnabled('SgDatatablesBundle')) {
            $io = new CommandStyle($input, $output);
            $io->warning('SgDatatablesBundle is not enabled.');
            if (!$io->confirm('Do you still want to proceed?')) {
                return;
            }
        }
        parent::execute($input, $output);
    }

    protected function generateFiles(MetaEntityInterface $metaEntity): array
    {
        return $this->crudGenerator->createVoter($metaEntity);
    }

    protected function askQuestions(InputInterface $input, OutputInterface $output): void
    {
        return;
    }
}
