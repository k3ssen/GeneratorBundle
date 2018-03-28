<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\EntityQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\Command\PropertyQuestion\PropertyQuestionInterface;
use K3ssen\GeneratorBundle\MetaData\MetaEntityFactory;
use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyInterface;

class FieldsQuestion implements EntityQuestionInterface
{
    public const PRIORITY = 50;

    /** @var MetaEntityFactory */
    protected $metaEntityFactory;

    /** @var iterable|PropertyQuestionInterface[] */
    protected $propertyQuestions;

    public function __construct(MetaEntityFactory $metaEntityFactory, iterable $propertyQuestions) {
        $this->metaEntityFactory = $metaEntityFactory;
        $this->propertyQuestions = $propertyQuestions;
    }

    public function addActions(CommandInfo $commandInfo, array &$actions) {
        $actions['New field'] = function() use($commandInfo) { $this->addNewField($commandInfo); };
        $actions['Edit field'] = function() use($commandInfo) { $this->editField($commandInfo); };
        $actions['Remove field'] = function() use($commandInfo) { $this->removeField($commandInfo); };
    }

    public function doQuestion(CommandInfo $commandInfo)
    {
        $this->addNewField($commandInfo);
    }

    public function addNewField(CommandInfo $commandInfo)
    {
        do {
            $commandInfo->getIo()->section('Add new field');
            $previousMetaProperty = $commandInfo->getMetaEntity()->getProperties()->last() ?: null;
            $metaProperty = null;
            foreach ($this->propertyQuestions as $index => $propertyQuestion) {
                if ($index === 0) {
                    $propertyQuestion->doQuestion($commandInfo, $metaProperty);
                    $metaProperty = $commandInfo->getMetaEntity()->getProperties()->last() ?: null;
                    if ($metaProperty === $previousMetaProperty) {
                        return;
                    }
                } else {
                    $propertyQuestion->doQuestion($commandInfo, $metaProperty);
                }
                $commandInfo->saveTemporaryFile();
            }
        } while(true);
    }

    public function editField(CommandInfo $commandInfo, MetaPropertyInterface $metaProperty = null)
    {
        if (!$metaProperty) {
            $commandInfo->getIo()->section('Edit field');
            $metaProperty = $this->chooseField($commandInfo);
            if (!$metaProperty) {
                return;
            }
        }
        foreach ($this->propertyQuestions as $propertyQuestion) {
            $propertyQuestion->doQuestion($commandInfo, $metaProperty);
            $commandInfo->saveTemporaryFile();
        }
    }

    public function removeField(CommandInfo $commandInfo, MetaPropertyInterface $metaProperty = null)
    {
        if (!$metaProperty) {
            $commandInfo->getIo()->section('Remove field');
            $metaProperty = $this->chooseField($commandInfo);
            if (!$metaProperty) {
                return;
            }
        }
        if ($commandInfo->getMetaEntity()->getDisplayProperty() === $metaProperty) {
            $commandInfo->getMetaEntity()->setDisplayProperty(null);
        }
        $commandInfo->getMetaEntity()->removeProperty($metaProperty);
        unset($metaProperty);
        $commandInfo->saveTemporaryFile();
    }

    protected function chooseField(CommandInfo $commandInfo): ?MetaPropertyInterface
    {
        $properties = $commandInfo->getMetaEntity()->getProperties();
        $propertyChoice = $commandInfo->getIo()->choice(
            'Choice (press <comment>[enter]</comment> to cancel)',
            array_merge([null], $properties->toArray())
        );
        if (!$propertyChoice) {
            return null;
        }
        foreach ($properties as $property) {
            if ($property->getName() === $propertyChoice) {
                return $property;
            }
        }
        throw new \RuntimeException(sprintf('No property found for choice %s', $propertyChoice));
    }
}