<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\ClassAnnotation;


use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

interface MetaAnnotationInterface
{
    public function __construct(MetaEntityInterface $metaEntity, $namespace, string $annotationName = null, array $annotationAttribute = []);

    public function getAnnotationName(): string;

    public function getNamespace(): string;

    public function getNamespaceAlias(): ?string;

    public function getAnnotationAttributes(): array;

    public function addAnnotationAttribute(string $name, $value);
}