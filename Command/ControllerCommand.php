<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use K3ssen\GeneratorBundle\Generator\CrudGenerator;
use K3ssen\GeneratorBundle\Generator\CrudGenerateOptions;
use K3ssen\GeneratorBundle\MetaData\MetaEntityFactory;
use K3ssen\GeneratorBundle\Reader\ExistingEntityToMetaEntityReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CrudCommand extends Command
{
    protected static $defaultName = 'generator:create:controller';

    /** @var ExistingEntityToMetaEntityReader */
    protected $existingEntityToMetaEntityReader;

    /** @var MetaEntityFactory */
    protected $metaEntityFactory;

    /** @var CrudGenerator */
    protected $crudGenerator;

    protected $bundles;

    public function __construct(
        ?string $name = null,
        CrudGenerator $crudGenerator,
        MetaEntityFactory $metaEntityFactory,
        ExistingEntityToMetaEntityReader $existingEntityToMetaEntityReader,
        array $bundles
    ) {
        parent::__construct($name);
        $this->crudGenerator = $crudGenerator;
        $this->metaEntityFactory = $metaEntityFactory;
        $this->existingEntityToMetaEntityReader = $existingEntityToMetaEntityReader;
        $this->bundles = $bundles;
    }

    protected function configure()
    {
        $this->setDescription('Generate crud for existing entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Generate Controller');
        $choices = $this->metaEntityFactory->getEntityOptions();
        if (count($choices) === 0) {
            $io->error('No entities found; Add some entities first.');
            return;
        }
        $choice = $io->choice('Entity', $choices, $input->getArgument('entity'));
        $metaEntity = $this->metaEntityFactory->getMetaEntityByChosenOption($choice);

        $generateOptions = new CrudGenerateOptions();

        $generateOptions->setUsingWriteActions($io->confirm('Include write actions (new, edit, delete)?', true));

        if (array_key_exists('SgDatatablesBundle', $this->bundles)) {
            $generateOptions->setUsingDatatable($io->confirm('Generate Datatable class?', true));
        } else {
            $generateOptions->setUsingDatatable(false);
        }

        if (array_key_exists('SecurityBundle', $this->bundles)) {
            $generateOptions->setUsingVoters($io->confirm('Generate Voter class?', true));
        } else {
            $generateOptions->setUsingVoters(false);
        }

        $this->existingEntityToMetaEntityReader->extractExistingClassToMetaEntity($metaEntity);

        $files = $this->crudGenerator->createCrud($metaEntity, $generateOptions);
        foreach ($files as $file) {
            $io->success(sprintf('Created/Updated file %s', $file));
        }
    }
}
