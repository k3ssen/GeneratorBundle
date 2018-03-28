<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData;

use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyInterface;

interface MetaAttributeInterface
{
    public function __construct(string $name);

    public function getMetaProperty(): ?MetaPropertyInterface;

    public function setMetaProperty(MetaPropertyInterface $metaProperty);

    public function getName(): ?string;

    public function isNullable(): bool;

    public function setNullable(bool $nullable);

    public function getType(): ?string;

    public function isBool(): bool;

    public function isInt(): bool;

    public function setType(string $type);

    public function getDefaultValue();

    public function setDefaultValue($defaultValue);

    public function getValue();

    public function setValue($value);

    public function getValueIsSet(): ?bool;

    public function setValueIsSet(bool $valueIsSet = true);
}
