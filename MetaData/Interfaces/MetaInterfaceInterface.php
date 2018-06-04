<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Interfaces;

use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

interface MetaInterfaceInterface
{
    public function __construct(MetaEntityInterface $metaEntity, $namespace, string $interfaceUsage = null);

    public function getMetaEntity(): MetaEntityInterface;

    public function getNamespace(): string;

    public function getNamespaceAlias(): ?string;

    public function getInterfaceUsage(): string;
}