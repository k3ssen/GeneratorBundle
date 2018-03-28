<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\EntityQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\MetaData\Property\PrimitiveMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\RelationMetaPropertyInterface;

class StatusQuestion implements EntityQuestionInterface
{
    public const PRIORITY = 10;

    public function addActions(CommandInfo $commandInfo, array &$actions) {
        $actions['Show info'] = function() use($commandInfo) { $this->showInfo($commandInfo); };
    }

    public function doQuestion(CommandInfo $commandInfo)
    {
        //Do nothing
    }

    public function showInfo(CommandInfo $commandInfo)
    {
        $commandInfo->getIo()->text(sprintf('<info>Current Entity:</info> %s', $commandInfo->getMetaEntity()->getName()));
        $propertyOutputs = [];
        foreach ($commandInfo->getMetaEntity()->getProperties() as $property) {
            $validationNames = [];
            foreach ($property->getValidations() as $validation) {
                $validationNames[] = $validation->getClassShortName();
            }
            $propertyOutputs[] = [
                $property->getName(),
                $property->getOrmType()
                    . ($property instanceof RelationMetaPropertyInterface ? '<comment> ['.$property->getTargetEntity().']</comment>' : '')
                    . ($property instanceof PrimitiveMetaPropertyInterface && $property->isId() ? ' [<comment>id</comment>]' : '')
                    . ($commandInfo->getMetaEntity()->getDisplayProperty() === $property ? ' [<comment>display field</comment>]' : '')
                    . ($property instanceof PrimitiveMetaPropertyInterface && $property->isNullable() ? ' <comment>[nullable]</comment>' : '')
                ,
                implode(', ', $validationNames),
            ];
        }
        $commandInfo->getIo()->table(['Property', 'Type', 'Validations'], $propertyOutputs);
    }
}