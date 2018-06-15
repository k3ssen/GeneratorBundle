<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData;

use K3ssen\GeneratorBundle\MetaData\ClassAnnotation\MetaAnnotationInterface;
use K3ssen\GeneratorBundle\MetaData\Interfaces\MetaInterfaceInterface;
use K3ssen\GeneratorBundle\MetaData\Property\PrimitiveMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\RelationMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Traits\MetaTraitInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface MetaEntityInterface
{
    public const NO_BUNDLE_NAMESPACE = 'App';

    public function __construct(string $nameOrFullClassName);

    public function getName(): string;

    public function setName(string $name);

    public function getShortcutNotation();

    public function getBundleNamespace(): ?string;

    public function getBundleName(): ?string;

    public function setFullClassName($fullClassName);

    public function getFullClassName(): string;

    public function getNamespace(): string;

    public function setBundleNamespace(?string $bundleNamespace);

    public function getSubDir(): ?string;

    public function setSubDir(?string $subDir);

    public function getUsages(): ?array;

    public function addUsage(string $namespace, string $alias = null);

    /** @return static */
    public function removeUsage($namespace);

    /** @return MetaTraitInterface[] */
    public function getTraits(): array;

    /** @return static */
    public function addTrait(MetaTraitInterface $trait);

    /** @return static */
    public function removeTrait(MetaTraitInterface $trait);

    /** @return MetaInterfaceInterface[] */
    public function getInterfaces(): array;

    /** @return static */
    public function addInterface(MetaInterfaceInterface $interface);

    /** @return static */
    public function removeInterface(MetaInterfaceInterface $trait);

    public function getTableName(): ?string;

    public function getRepositoryFullClassName(): ?string;

    public function getRepositoryNamespace(): ?string;

    /** @return MetaAnnotationInterface[] */
    public function getEntityAnnotations(): array;

    /** @return static */
    public function addEntityAnnotation(MetaAnnotationInterface $entityAnnotation);

    /** @return static */
    public function removeEntityAnnotation(MetaAnnotationInterface $entityAnnotation);

    /** @return MetaPropertyInterface[]|ArrayCollection */
    public function getProperties(): ArrayCollection;

    public function addProperty(MetaPropertyInterface $property);

    public function removeProperty(MetaPropertyInterface $property);

    public function getCollectionProperties(): ArrayCollection;

    /** @return ArrayCollection|RelationMetaPropertyInterface[] */
    public function getRelationshipProperties(): ArrayCollection;

    public function getDisplayProperty(): ?PrimitiveMetaPropertyInterface;

    public function setDisplayProperty(?PrimitiveMetaPropertyInterface $displayProperty);

    public function getIdProperty(): ?PrimitiveMetaPropertyInterface;

    public function hasCustomRepository(): bool;

    public function setUseCustomRepository(bool $customRepository);

    public function __toString();
}