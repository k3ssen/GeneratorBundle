<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use K3ssen\GeneratorBundle\Command\Style\CommandStyle;
use K3ssen\GeneratorBundle\Generator\CrudGenerator;
use K3ssen\GeneratorBundle\Generator\CrudGenerateOptions;
use K3ssen\GeneratorBundle\MetaData\MetaEntityFactory;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\Reader\BundleProvider;
use K3ssen\GeneratorBundle\Reader\ExistingEntityToMetaEntityReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CrudCommand extends Command
{
    protected const TITLE = 'Generate CRUD';

    protected static $defaultName = 'generator:crud';

    /** @var ExistingEntityToMetaEntityReader */
    protected $existingEntityToMetaEntityReader;

    /** @var MetaEntityFactory */
    protected $metaEntityFactory;

    /** @var CrudGenerator */
    protected $crudGenerator;

    /** @var BundleProvider */
    protected $bundleProvider;

    /** @var CrudGenerateOptions */
    protected $generateOptions;

    public function __construct(
        ?string $name = null,
        CrudGenerator $crudGenerator,
        MetaEntityFactory $metaEntityFactory,
        ExistingEntityToMetaEntityReader $existingEntityToMetaEntityReader,
        BundleProvider $bundleProvider,
        CrudGenerateOptions $crudGenerateOptions
    ) {
        parent::__construct($name);
        $this->crudGenerator = $crudGenerator;
        $this->metaEntityFactory = $metaEntityFactory;
        $this->existingEntityToMetaEntityReader = $existingEntityToMetaEntityReader;
        $this->bundleProvider = $bundleProvider;
        $this->generateOptions = $crudGenerateOptions;
    }

    protected function configure()
    {
        $this->setDescription('Generate crud for existing entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('controller-subdirectory', null,InputOption::VALUE_OPTIONAL, 'Subdirectory for controller')
            ->addOption('use-voter', null,InputOption::VALUE_OPTIONAL)
            ->addOption('use-datatable', null,InputOption::VALUE_OPTIONAL)
            ->addOption('use-write-actions', null,InputOption::VALUE_OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->showStartInfo($input, $output);
        $metaEntity = $this->chooseEntity($input, $output);
        if (!$metaEntity) {
            return;
        }
        $this->askQuestions($input, $output);
        $this->process($input, $output, $metaEntity);
    }

    protected function process(InputInterface $input, OutputInterface $output, MetaEntityInterface $metaEntity)
    {
        $io = new CommandStyle($input, $output);
        $this->existingEntityToMetaEntityReader->extractExistingClassToMetaEntity($metaEntity);

        $files = $this->generateFiles($metaEntity);
        foreach ($files as $file) {
            $io->success(sprintf('Created/Updated file %s', $file));
        }
    }

    protected function generateFiles(MetaEntityInterface $metaEntity): array
    {
        return $this->crudGenerator->createCrud($metaEntity);
    }

    protected function showStartInfo(InputInterface $input, OutputInterface $output): void
    {
        $io = new CommandStyle($input, $output);
        $io->title(static::TITLE);
    }

    protected function chooseEntity(InputInterface $input, OutputInterface $output): ?MetaEntityInterface
    {
        $io = new CommandStyle($input, $output);
        $choices = $this->metaEntityFactory->getEntityOptionsAsStrings();
        if (count($choices) === 0) {
            $io->error('No entities found; Add some entities first.');
            return null;
        }
        $inputChoice = $input->getArgument('entity');
        if ($inputChoice && !in_array($inputChoice, $choices, true)) {
            $io->warning(sprintf('No entity found for "%s"', $inputChoice));
            $inputChoice = null;
        }
        $choice = $io->choice('Entity', $choices, $inputChoice);

        return $this->metaEntityFactory->getMetaEntityByChosenOption($choice);
    }

    protected function askQuestions(InputInterface $input, OutputInterface $output): void
    {
        $this->determineControllerSubDirectory($input, $output);
        $this->determineUseWriteActions($input, $output);
        $this->determineUseVoter($input, $output);
        $this->determineUseDatatable($input, $output);
    }

    protected function determineUseWriteActions(InputInterface $input, OutputInterface $output)
    {
        $io = new CommandStyle($input, $output);
        $useWriteActions = $input->getOption('use-write-actions') ?? $this->generateOptions->getUseWriteActionsDefault();
        if ($this->generateOptions->getAskUseWriteActions()) {
            $useWriteActions = $io->confirm('Include write actions (new, edit, delete)?', $useWriteActions);
        }
        $this->generateOptions->setUseWriteActions($useWriteActions);
    }

    protected function determineControllerSubDirectory(InputInterface $input, OutputInterface $output)
    {
        $io = new CommandStyle($input, $output);
        $subdirectory = $input->getOption('controller-subdirectory') ?? $this->generateOptions->getControllerSubdirectoryDefault();
        if ($this->generateOptions->getAskControllerSubdirectory()) {
            $subdirectory = $io->ask('Subdirectory for controller (optional)', $subdirectory);
            if (!$subdirectory || strtolower($subdirectory) === 'null' || $subdirectory === '~' || is_numeric($subdirectory)) {
                $subdirectory = null;
                $io->text('Using no subdirectory');
            } else {
                $io->text(sprintf('Using subdirectory "%s"', $subdirectory));
            }
        }
        $this->generateOptions->setControllerSubdirectory($subdirectory);
    }

    protected function determineUseVoter(InputInterface $input, OutputInterface $output)
    {
        $io = new CommandStyle($input, $output);
        $useVoter = $input->getOption('use-voter') ?? $this->generateOptions->getUseVoterDefault();
        if ($this->bundleProvider->isEnabled('SecurityBundle')) {
            if ($this->generateOptions->getAskUseVoter()) {
                $useVoter = $io->confirm('Use Voter?', $useVoter);
            }
        } elseif ($useVoter) {
            $io->warning('Cannot use voter: SecurityBundle is not enabled.');
            $useVoter = false;
        }
        $this->generateOptions->setUseVoter($useVoter);
    }

    protected function determineUseDatatable(InputInterface $input, OutputInterface $output)
    {
        $io = new CommandStyle($input, $output);
        $useDatatable = $input->getOption('use-datatable') ?? $this->generateOptions->getUseDatatableDefault();
        if ($this->bundleProvider->isEnabled('SgDatatablesBundle')) {
            if ($this->generateOptions->getAskUseDatatable()) {
                $useDatatable = $io->confirm('Use Datatable?', $useDatatable);
            }
        } elseif ($useDatatable) {
            $io->warning('Cannot generate datatable: SgDatatablesBundle is not enabled.');
            $useDatatable = false;
        }
        $this->generateOptions->setUseDatatable($useDatatable);
    }
}
