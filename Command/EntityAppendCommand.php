<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use K3ssen\GeneratorBundle\Command\EntityQuestion\FieldsQuestion;
use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\Generator\EntityAppender;
use K3ssen\GeneratorBundle\Generator\EntityGenerator;
use K3ssen\GeneratorBundle\MetaData\MetaEntityFactory;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class EntityAppendCommand extends Command
{
    protected static $defaultName = 'generator:entity:append';

    /** @var EntityAppender */
    protected $entityAppender;

    /** @var $fieldQuestion */
    protected $fieldsQuestion;

    /** @var EntityGenerator */
    protected $entityGenerator;

    /** @var MetaEntityFactory */
    protected $metaEntityFactory;

    public function __construct(
        ?string $name = null,
        MetaEntityFactory $metaEntityFactory,
        EntityAppender $entityAppender,
        EntityGenerator $entityGenerator,
        FieldsQuestion $fieldsQuestion
    ) {
        parent::__construct($name);
        $this->metaEntityFactory = $metaEntityFactory;
        $this->entityAppender = $entityAppender;
        $this->entityGenerator = $entityGenerator;
        $this->fieldsQuestion = $fieldsQuestion;
    }

    protected function configure()
    {
        $this->setDescription('Append fields to an existing entity')
            ->addOption('savepoint', 's', InputOption::VALUE_NONE, false)
            ->addOption('revert', 'r', InputOption::VALUE_NONE, false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandInfo = new CommandInfo($input, $output);

        $commandInfo->getIo()->title('Append fields to entity');

        if ($input->getOption('revert')) {
            $commandInfo->loadMetaEntityFromTemporaryFile();
            $metaEntity = $commandInfo->getMetaEntity();
            $this->revertFileByMetaEntity($metaEntity);
            $commandInfo->getIo()->success(sprintf('File for entity "%s" has been reverted', $metaEntity));
            return;
        }
        if ($input->getOption('savepoint')) {
            $commandInfo->loadMetaEntityFromTemporaryFile();
            $pseudoMetaEntity = $commandInfo->getMetaEntity();
        } else {
            $choices = $this->metaEntityFactory->getEntityOptions();
            if (count($choices) === 0) {
                $commandInfo->getIo()->error('No entities found; Use \'generator:entity\' instead to add new entities.');
            }
            $choice = $commandInfo->getIo()->choice('Entity', $choices);

            $pseudoMetaEntity = $this->metaEntityFactory->getMetaEntityByChosenOption($choice);
            $commandInfo->setMetaEntity($pseudoMetaEntity);
        }
        $this->fieldsQuestion->doQuestion($commandInfo);
        $this->checkExistingFields($commandInfo);
        $commandInfo->saveTemporaryFile();
        $this->backupFile($pseudoMetaEntity);

        $affectedFiles = $this->entityGenerator->updateEntity($pseudoMetaEntity);
        foreach ($affectedFiles as $filePath) {
            $commandInfo->getIo()->success(sprintf('Created/Updated file %s', $filePath));
        }
    }

    protected function backupFile(MetaEntityInterface $metaEntity)
    {
        $reflector = new \ReflectionClass($metaEntity->getFullClassName());
        $content = file_get_contents($reflector->getFileName());
        $temp = sys_get_temp_dir(). '/entity_backup';
        file_put_contents($temp, $content);
    }

    protected function revertFileByMetaEntity(MetaEntityInterface $metaEntity)
    {
        $reflector = new \ReflectionClass($metaEntity->getFullClassName());
        $temp = sys_get_temp_dir(). '/entity_backup';
        if (!file_exists($temp)) {
            throw new FileNotFoundException('No backup file available.');
        }
        $content = file_get_contents($temp);
        file_put_contents($reflector->getFileName(), $content);
    }

    protected function checkExistingFields(CommandInfo $commandInfo)
    {
        $metaEntity = $commandInfo->getMetaEntity();
        $reflector = new \ReflectionClass($metaEntity->getFullClassName());
        foreach($reflector->getProperties() as $reflectorProperty) {
            foreach ($metaEntity->getProperties() as $metaProperty) {
                if ($metaProperty->getName() === $reflectorProperty->getName()) {
                    $commandInfo->getIo()->error(sprintf('The property "%s" is already defined in entity "%s".', $metaProperty, $metaEntity));
                    $doEdit = $commandInfo->getIo()->confirm('Edit this field? Choose "no" to remove this field');
                    if ($doEdit) {
                        $this->fieldsQuestion->editField($commandInfo, $metaProperty);
                    } else {
                        $this->fieldsQuestion->removeField($commandInfo, $metaProperty);
                    }
                    $this->checkExistingFields($commandInfo);
                    return;
                }
            }
        };
    }
}
