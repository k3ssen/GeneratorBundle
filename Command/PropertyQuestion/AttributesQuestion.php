<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\PropertyQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\Command\AttributeQuestion\AttributeQuestionInterface;
use K3ssen\GeneratorBundle\Command\Helper\EvaluationTrait;
use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyInterface;

class AttributesQuestion implements PropertyQuestionInterface
{
    public const PRIORITY = 10;

    use EvaluationTrait;

    /** @var iterable|AttributeQuestionInterface[] */
    protected $attributeQuestions;

    public function __construct(iterable $attributeQuestions)
    {
        $this->attributeQuestions = $attributeQuestions;
    }

    public function doQuestion(CommandInfo $commandInfo, MetaPropertyInterface $metaProperty = null)
    {
        foreach ($metaProperty->getMetaAttributes() as $metaAttribute) {
            foreach ($this->attributeQuestions as $attributeQuestion) {
                if ($attributeQuestion->supportsAttribute($metaAttribute->getName())) {
                    $attributeQuestion->doQuestion($commandInfo, $metaAttribute);
                    $commandInfo->saveTemporaryFile();
                }
            }
        }
    }
}