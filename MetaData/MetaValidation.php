<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData;

use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyInterface;

class MetaValidation implements MetaValidationInterface
{
    /** @var MetaPropertyInterface */
    protected $metaProperty;

    /** @var string */
    protected $className;

    /** @var array */
    protected $options;

    public function __construct(MetaPropertyInterface $metaProperty, string $className, array $options = [])
    {
        $metaProperty->getMetaEntity()->addUsage('Symfony\Component\Validator\Constraints', 'Assert');
        $this->setMetaProperty($metaProperty);
        $this->setClassName($className);
        $this->setOptions($options);
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function setClassName(string $className): self
    {
        $this->className = $className;
        return $this;
    }

    public function getClassShortName(): ?string
    {
        $parts = explode('\\', $this->className);
        return array_pop($parts);
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function getAnnotationFormatted(): string
    {
        $formattedString = '@Assert\\'.$this->getClassShortName();
        $numberOfOptions = count($this->getOptions());
        if ($numberOfOptions === 0) {
            return $formattedString;
        }
        $formattedString .= "(";
        $first = true;
        foreach ($this->getOptions() as $optionName => $optionValue) {
            if ($first === false) {
                $formattedString .= ', ';
            }
            $first = false;
            if ($numberOfOptions > 1) {
                $formattedString .= $optionName.'=';
            }
            if (is_array($optionValue)) {
                $formattedString .= $this->arrayContainsIntOnly($optionValue)
                    ? implode(', ', $optionValue)
                    : ('"'.implode('", "', $optionValue) ).'"';
            } else {
                $formattedString .= is_numeric($optionValue) ? $optionValue : ('"'.$optionValue.'"');
            }
        }
        $formattedString .= ")";

        return $formattedString;
    }

    protected function arrayContainsIntOnly(array $values): bool
    {
        foreach ($values as $value) {
            if (is_int($value) === false) {
                return false;
            }
        }
        return true;
    }

    public function getMetaProperty(): ?MetaPropertyInterface
    {
        return $this->metaProperty;
    }

    public function setMetaProperty(MetaPropertyInterface $metaProperty)
    {
        $this->metaProperty = $metaProperty;
        $metaProperty->addValidation($this);
    }

    public function __toString()
    {
        return $this->getClassName();
    }
}
