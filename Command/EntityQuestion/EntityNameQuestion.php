<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\EntityQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\MetaData\MetaEntityFactory;

class EntityNameQuestion implements EntityQuestionInterface
{
    public const PRIORITY = 99;

    /** @var MetaEntityFactory */
    protected $metaEntityFactory;

    public function __construct(MetaEntityFactory $metaEntityFactory)
    {
        $this->metaEntityFactory = $metaEntityFactory;
    }

    public function addActions(CommandInfo $commandInfo, array &$actions) {
        $actions['Edit entity name'] = function() use($commandInfo) { $this->doQuestion($commandInfo); };
    }

    public function doQuestion(CommandInfo $commandInfo)
    {
        try {
            $metaEntity = $commandInfo->getMetaEntity();
            $entityName = $metaEntity->getName();
        } catch (\RuntimeException $exception) {
            $entityName = $commandInfo->getInput()->getArgument('entity');
        }
        $nameAnswer = $commandInfo->getIo()->ask('Entity name', $entityName, function ($value) {
            if (!$value) {
                throw new \InvalidArgumentException('The entity name cannot be empty');
            }
            return $value;
        });
        if (isset($metaEntity)) {
            $metaEntity->setName($nameAnswer);
            return;
        }
        try {
            $metaEntity = $this->metaEntityFactory->createByShortcutNotation($nameAnswer);
            $commandInfo->setMetaEntity($metaEntity);
        } catch (\InvalidArgumentException $exception) {
            $commandInfo->getIo()->error($exception->getMessage());
            $this->doQuestion($commandInfo);
        }
    }
}