<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use K3ssen\GeneratorBundle\Command\Style\CommandStyle;
use K3ssen\GeneratorBundle\Generator\EntityGenerator;
use K3ssen\GeneratorBundle\MetaData\MetaEntityFactory;
use K3ssen\GeneratorBundle\Reader\ExistingEntityToMetaEntityReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RepositoryCommand extends Command
{
    protected static $defaultName = 'generator:repository';

    /** @var ExistingEntityToMetaEntityReader */
    protected $existingEntityToMetaEntityReader;

    /** @var MetaEntityFactory */
    protected $metaEntityFactory;

    /** @var EntityGenerator */
    protected $entityGenerator;

    public function __construct(
        ?string $name = null,
        EntityGenerator $entityGenerator,
        MetaEntityFactory $metaEntityFactory,
        ExistingEntityToMetaEntityReader $existingEntityToMetaEntityReader
    ) {
        parent::__construct($name);
        $this->entityGenerator = $entityGenerator;
        $this->metaEntityFactory = $metaEntityFactory;
        $this->existingEntityToMetaEntityReader = $existingEntityToMetaEntityReader;
    }

    protected function configure()
    {
        $this->setDescription('Generate crud for existing entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new CommandStyle($input, $output);

        $io->title('Generate Repository');
        $choices = $this->metaEntityFactory->getEntityOptions();
        if (count($choices) === 0) {
            $io->error('No entities found; Add some entities first.');
            return;
        }
        $choice = $io->choice('Entity', $choices, $input->getArgument('entity'));
        $metaEntity = $this->metaEntityFactory->getMetaEntityByChosenOption($choice);

        $this->existingEntityToMetaEntityReader->extractExistingClassToMetaEntity($metaEntity);

        $file = $this->entityGenerator->createRepository($metaEntity);
        $io->success(sprintf('Created/Updated file %s', $file));
    }
}
