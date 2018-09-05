<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use K3ssen\GeneratorBundle\Command\Style\CommandStyle;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TemplatesCommand extends CrudCommand
{
    protected const TITLE = 'Generate Templates (twig view files)';

    protected const VALID_ACTIONS = ['index', 'show', 'new', 'edit', 'delete'];

    protected static $defaultName = 'generator:templates';

    protected $selectedActions;

    protected function configure()
    {
        $this->setDescription('Generate templates (twig view files) for existing entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Entity name you want to create templates for')
            ->addArgument('actions', InputArgument::IS_ARRAY, 'Actions to generate files for (index, show, new, edit, delete). Defaults to all actions')
            ->addOption('controller-subdirectory', null,InputOption::VALUE_OPTIONAL, 'Subdirectory for controller')
        ;
    }

    protected function generateFiles(MetaEntityInterface $metaEntity): array
    {
        $actions = $this->selectedActions ?: static::VALID_ACTIONS;
        $files = [];
        foreach ($actions as $action) {
            $files[] = $this->crudGenerator->createViewTemplate($metaEntity, $action);
        }
        return $files;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->selectedActions = $input->getArgument('actions') ?: [];
        foreach ($this->selectedActions as $action) {
            if (!in_array($action, static::VALID_ACTIONS, true)) {
                $io = new CommandStyle($input, $output);
                $io->warning(sprintf('Action "%s" is not valid; Valid actions are: %s', $action, implode(', ', static::VALID_ACTIONS)));
            }
        }
        $inputChoice = $input->getArgument('entity');
        if (in_array($inputChoice, static::VALID_ACTIONS)) {
            $input->setArgument('entity', null);
            array_unshift ($this->selectedActions, $inputChoice);
        }

        return parent::execute($input, $output);
    }

    protected function askQuestions(InputInterface $input, OutputInterface $output): void
    {
        $this->determineControllerSubDirectory($input, $output);
    }
}
