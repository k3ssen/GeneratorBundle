<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\EntityQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;

class DisplayFieldQuestion implements EntityQuestionInterface
{
    public const PRIORITY = 40;
    /**
     * @var bool
     */
    protected $askDisplayField;

    public function __construct(bool $askDisplayField)
    {
        $this->askDisplayField = $askDisplayField;
    }

    public function addActions(CommandInfo $commandInfo, array &$actions) {
        if ($this->askDisplayField) {
            $actions['Edit display field'] = function () use ($commandInfo) {
                $this->doQuestion($commandInfo);
            };
        }
    }

    public function doQuestion(CommandInfo $commandInfo)
    {
        if ($this->askDisplayField === false) {
            return;
        }
        $propertyOptions = ['' => null];
        foreach ($commandInfo->getMetaEntity()->getProperties() as $property) {
            if (in_array($property->getReturnType(), ['string', 'int'], true)) {
                $propertyOptions[$property->getName()] = $property;
            }
        }
        if (count($propertyOptions) === 1) {
            $commandInfo->getIo()->note('Currently, there are no properties suitable for using as display field.');
            return;
        }
        $defaultDisplayField = $commandInfo->getMetaEntity()->getDisplayProperty();
        $answer = $commandInfo->getIo()->choice('Display field (optional)', array_keys($propertyOptions), $defaultDisplayField ? (string) $defaultDisplayField : null);
        $property = $propertyOptions[$answer] ?? $answer;
        $commandInfo->getMetaEntity()->setDisplayProperty($property === '' ? null : $property);
    }
}