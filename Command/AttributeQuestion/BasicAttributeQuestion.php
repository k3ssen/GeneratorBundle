<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\AttributeQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\Command\Helper\EvaluationTrait;
use K3ssen\GeneratorBundle\MetaData\PropertyAttribute\MetaAttributeInterface;

class BasicAttributeQuestion implements AttributeQuestionInterface
{
    use EvaluationTrait;

    protected $supportedAttributes;

    public function addAttribute(string $attributeName, array $attributeInfo = [])
    {
        $this->supportedAttributes[$attributeName] = $attributeInfo;
    }

    public function supportsAttribute(string $attributeName): bool
    {
        return array_key_exists($attributeName, $this->supportedAttributes);
    }

    protected function getRequirementExpression(string $attributeName): ?string
    {
        return $this->supportedAttributes[$attributeName]['requirement_expression'] ?? null;
    }

    protected function getValidationExpression(string $attributeName): ?string
    {
        return $this->supportedAttributes[$attributeName]['validation_expression'] ?? null;
    }

    public function doQuestion(CommandInfo $commandInfo, MetaAttributeInterface $metaAttribute)
    {
        if ($requirement = $this->getRequirementExpression($metaAttribute->getName())) {
            $requirementResult = $this->evaluateMetaAttribute($metaAttribute, $requirement);
            if ($requirementResult === false) {
                return;
            }
        }

        if ($metaAttribute->isBool()) {
            $value = $commandInfo->getIo()->confirm(ucfirst($metaAttribute->getName()), $metaAttribute->getValue() !== false);
            $metaAttribute->setValue($value);
            return;
        }

        $question = ucfirst($metaAttribute->getName()) . ($metaAttribute->isNullable() ? ' (optional)': '');
        $value = $commandInfo->getIo()->ask($question, $metaAttribute->getValue(), function ($value) use ($metaAttribute, $commandInfo) {
            if (!$metaAttribute->isNullable() && $value === null) {
                throw new \InvalidArgumentException('This value cannot be null');
            }
            if ($metaAttribute->isInt()) {
                if ($value !== null && !is_numeric($value)) {
                    throw new \InvalidArgumentException('This value must be a number');
                }
                $value = (int) $value;
            }

            if ($validation = $this->getValidationExpression($metaAttribute->getName())) {
                $validationResult = $this->evaluateMetaAttribute($metaAttribute, $validation);
                if (!$validationResult) {
                    throw new \InvalidArgumentException(sprintf('Value evaluated false by validation expression "%s"', $validation));
                }
            }
            return $value;
        });
        $metaAttribute->setValue($value);
    }
}