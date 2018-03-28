<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\Common\Collections\ArrayCollection;
use K3ssen\GeneratorBundle\MetaData\MetaAttributeInterface;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\MetaData\MetaValidationInterface;

interface MetaPropertyInterface
{
    public function __construct(MetaEntityInterface $metaEntity, ArrayCollection $metaAttributes, string $name);

    public static function getReturnType(): string;

    public static function getOrmType();

    public static function getOrmTypeAlias();

    public function getMetaEntity(): MetaEntityInterface;

    public function setMetaEntity(MetaEntityInterface $metaEntity);

    public function getName(): ?string;

    public function setName(string $name);

    public function isNullable(): ?bool;

    public function setNullable(?bool $nullable);

    public function isUnique(): ?bool;

    public function setUnique(?bool $unique);

    /** @return ArrayCollection|MetaValidationInterface[] */
    public function getValidations(): ArrayCollection;

    public function setValidations(ArrayCollection $validations);

    public function addValidation(MetaValidationInterface $validation);

    public function removeValidation(MetaValidationInterface $validation);

    public function isHasValidation(): bool;

    /** @return array|ArrayCollection|MetaAttributeInterface[] */
    public function getMetaAttributes(): ArrayCollection;

    public function addMetaAttribute(MetaAttributeInterface $metaAttribute);

    public function removeMetaAttribute(MetaAttributeInterface $metaAttribute);

    public function getMetaAttribute($name): MetaAttributeInterface;

    public function hasAttribute($name): bool;

    public function getAttribute($name);

    public function setAttribute($name, $value);

    public function getAnnotationLines(): array;

    public function __toString();
}
