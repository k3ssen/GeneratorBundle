<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\PropertyAttribute;

use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyInterface;

class MetaAttribute implements MetaAttributeInterface
{
    protected const TYPE_STRING = 'string';
    protected const TYPE_INT = 'int';
    protected const TYPE_BOOL = 'bool';
    protected const TYPE_ARRAY = 'array';
    protected const TYPE_OBJECT = 'object';

    protected const ALLOWED_TYPES = [
        self::TYPE_STRING,
        self::TYPE_INT,
        self::TYPE_BOOL,
        self::TYPE_ARRAY,
        self::TYPE_OBJECT,
    ];

    /** @var MetaPropertyInterface */
    protected $metaProperty;

    /** @var string */
    protected $name;

    /** @var bool */
    protected $nullable = true;

    /** @var string */
    protected $type = 'string';

    //(optional) value that will be used if no value has been set yet.
    protected $defaultValue;

    //The actual value that will be used, unless it was never set (in which case we use defaultValue)
    protected $value;

    //Helps us know that the value has been set and that we should not use the default value
    protected $valueIsSet = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getMetaProperty(): ?MetaPropertyInterface
    {
        return $this->metaProperty;
    }

    public function setMetaProperty(MetaPropertyInterface $metaProperty)
    {
        $this->metaProperty = $metaProperty;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function setNullable(bool $nullable)
    {
        $this->nullable = $nullable;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isBool(): bool
    {
        return $this->getType() === static::TYPE_BOOL;
    }

    public function isInt(): bool
    {
        return $this->getType() === static::TYPE_INT;
    }

    public function setType(string $type)
    {
        if (!in_array($type, static::ALLOWED_TYPES, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Type "%s" is not allowed; The following types are allowed: %s',
                $type,
                implode(', ', static::ALLOWED_TYPES)
            ));
        }
        $this->type = $type;
        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function getValue()
    {
        if ($this->getValueIsSet()) {
            return $this->value;
        }
        return $this->getDefaultValue();
    }

    public function setValue($value)
    {
        if ($this->isNullable() && !is_array($value) && in_array(strtolower((string) $value), ['null', '~'])) {
            $this->value = null;
            return $this;
        }
        switch (strtolower($this->getType())) {
            case static::TYPE_BOOL:
                if (in_array(strtolower((string) $value), ['false', 'f', 'no', 'n'])) {
                    $value = false;
                } elseif (in_array(strtolower((string) $value), ['true', 't', 'yes', 'y'])) {
                    $value = true;
                }
                $value = (bool) $value;
                break;
            case static::TYPE_INT:
                $value = (int) $value;
                break;
            case static::TYPE_ARRAY:
                if (!is_array($value)) {
                    $value = str_replace([' ,', ' , ', ', '], ',', (string)$value);
                    $value = explode(',', $value);
                }
                break;
            case static::TYPE_STRING:
                $value = (string) $value;
                break;
        }
        $this->value = $value;
        $this->setValueIsSet(true);
        return $this;
    }

    public function getValueIsSet(): ?bool
    {
        return $this->valueIsSet;
    }

    public function setValueIsSet(bool $valueIsSet = true)
    {
        $this->valueIsSet = $valueIsSet;
        return $this;
    }
}
