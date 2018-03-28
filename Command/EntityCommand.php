<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\Command\EntityQuestion\EntityQuestionInterface;
use K3ssen\GeneratorBundle\Generator\EntityGenerator;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use LogicException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EntityCommand extends Command
{
    protected static $defaultName = 'generator:create:entity';

    /** @var EntityGenerator */
    protected $entityGenerator;

    /** @var iterable|EntityQuestionInterface[] */
    protected $entityQuestions;

    public function __construct(
        ?string $name = null,
        EntityGenerator $entityGenerator,
        iterable $entityQuestions
    ) {
        parent::__construct($name);
        $this->entityGenerator = $entityGenerator;
        $this->entityQuestions = $entityQuestions;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create an entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('savepoint', 's', InputOption::VALUE_NONE, 'Load the last entity information that was created during interaction')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandInfo = new CommandInfo($input, $output);

        $metaEntity = $this->makeEntity($commandInfo);

        $affectedFiles = $this->entityGenerator->createEntity($metaEntity);

        foreach ($affectedFiles as $fileName) {
            $commandInfo->getIo()->success(sprintf('Created/updated file %s', $fileName));
        }
    }

    protected function makeEntity(CommandInfo $commandInfo): ?MetaEntityInterface
    {
        $commandInfo->getIo()->title('Create new entity');

        if ($useSavePoint = $commandInfo->getInput()->getOption('savepoint')) {
            $commandInfo->loadMetaEntityFromTemporaryFile();
            $commandInfo->getIo()->text(sprintf('Use savepoint entity "%s"', (string) $commandInfo->getMetaEntity()));
        }
        $actions = [];
        foreach ($this->entityQuestions as $entityQuestion) {
            if (!$entityQuestion instanceof EntityQuestionInterface) {
                throw new LogicException(sprintf('Service "%s" is used as entityQuestion, but does not implement %s', get_class($entityQuestion), EntityQuestionInterface::class));
            }
            $entityQuestion->addActions($commandInfo, $actions);
            if (!$useSavePoint) {
                $entityQuestion->doQuestion($commandInfo);
                $commandInfo->saveTemporaryFile();
            }
        }
        $actions['All done! Generate entity!'] = null;
        do {
            $chosenAction = $commandInfo->getIo()->choice('Next action', array_keys($actions));
            $nextAction = $actions[$chosenAction] ?? null;
            if ($nextAction) {
                $nextAction();
                $commandInfo->saveTemporaryFile();
            }
        } while ($nextAction);

        return $commandInfo->getMetaEntity();
    }
}
