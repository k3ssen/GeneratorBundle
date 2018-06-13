<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use K3ssen\GeneratorBundle\Command\Style\CommandStyle;
use K3ssen\GeneratorBundle\Generator\CrudGenerator;
use K3ssen\GeneratorBundle\Generator\CrudGenerateOptions;
use K3ssen\GeneratorBundle\MetaData\MetaEntityFactory;
use K3ssen\GeneratorBundle\Reader\BundleProvider;
use K3ssen\GeneratorBundle\Reader\ExistingEntityToMetaEntityReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CrudCommand extends Command
{
    protected static $defaultName = 'generator:crud';

    /** @var ExistingEntityToMetaEntityReader */
    protected $existingEntityToMetaEntityReader;

    /** @var MetaEntityFactory */
    protected $metaEntityFactory;

    /** @var CrudGenerator */
    protected $crudGenerator;

    /** @var bool */
    protected $askVoter;

    /** @var bool */
    protected $askControllerSubdirectory;

    /** @var null|string */
    protected $defaultControllerSubdirectory;

    /** @var bool */
    protected $useVoterDefault;
    /**
     * @var BundleProvider
     */
    protected $bundleProvider;

    public function __construct(
        ?string $name = null,
        CrudGenerator $crudGenerator,
        MetaEntityFactory $metaEntityFactory,
        ExistingEntityToMetaEntityReader $existingEntityToMetaEntityReader,
        BundleProvider $bundleProvider,
        bool $askVoter,
        bool $useVoterDefault,
        bool $askControllerSubdirectory,
        ?string $defaultControllerSubdirectory
    ) {
        parent::__construct($name);
        $this->crudGenerator = $crudGenerator;
        $this->metaEntityFactory = $metaEntityFactory;
        $this->existingEntityToMetaEntityReader = $existingEntityToMetaEntityReader;
        $this->bundleProvider = $bundleProvider;
        // TODO: refactor these config settings into a new class/service
        $this->askVoter = $askVoter;
        $this->askControllerSubdirectory = $askControllerSubdirectory;
        $this->defaultControllerSubdirectory = $defaultControllerSubdirectory;
        $this->useVoterDefault = $useVoterDefault;
    }

    protected function configure()
    {
        $this->setDescription('Generate crud for existing entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('controller-subdirectory', null,InputOption::VALUE_OPTIONAL, 'Subdirectory for controller')
            ->addOption('use-voter', null,InputOption::VALUE_OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new CommandStyle($input, $output);
        $io->title('Generate CRUD');
        $choices = $this->metaEntityFactory->getEntityOptions();
        if (count($choices) === 0) {
            $io->error('No entities found; Add some entities first.');
            return;
        }
        $choice = $io->choice('Entity', $choices, $input->getArgument('entity'));
        $metaEntity = $this->metaEntityFactory->getMetaEntityByChosenOption($choice);

        $generateOptions = new CrudGenerateOptions();

        $this->determineControllerSubDirectory($input, $io, $generateOptions);

        $generateOptions->setUsingWriteActions($io->confirm('Include write actions (new, edit, delete)?', true));

        $this->determineUseVoter($input, $io, $generateOptions);


        $this->existingEntityToMetaEntityReader->extractExistingClassToMetaEntity($metaEntity);

        $files = $this->crudGenerator->createCrud($metaEntity, $generateOptions);
        foreach ($files as $file) {
            $io->success(sprintf('Created/Updated file %s', $file));
        }
    }

    protected function determineControllerSubDirectory(InputInterface $input, SymfonyStyle $io, CrudGenerateOptions $generateOptions)
    {
        $subdir = $input->getOption('controller-subdirectory');
        $subdir = $subdir ?: $this->defaultControllerSubdirectory;
        if ($this->askControllerSubdirectory) {
            $subdir = $io->ask('Subdirectory for controller (optional)', $subdir);
            if (!$subdir || strtolower($subdir) === 'null' || $subdir === '~' || is_numeric($subdir)) {
                $subdir = null;
                $io->text('Using no subdirectory');
            } else {
                $io->text(sprintf('Using subdirectory "%s"', $subdir));
            }
        }
        $generateOptions->setControllerSubdirectory($subdir);
    }

    protected function determineUseVoter(InputInterface $input, SymfonyStyle $io, CrudGenerateOptions $generateOptions)
    {
        if ($this->bundleProvider->isEnabled('SecurityBundle')) {
            $useVoter = $input->getOption('use-voter');
            $useVoter = $useVoter !== null ? $useVoter : $this->useVoterDefault;
            if ($this->askVoter) {
                $useVoter = $io->confirm('Generate Voter class?', true);
            }
            $generateOptions->setUsingVoters($useVoter);
        } else {
            $generateOptions->setUsingVoters(false);
        }
    }
}
