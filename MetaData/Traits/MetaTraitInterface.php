<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Traits;

use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

interface MetaTraitInterface
{
    public function __construct(MetaEntityInterface $metaEntity, $namespace, string $traitUsage = null);

    public function getMetaEntity(): MetaEntityInterface;

    public function getNamespace(): string;

    public function getNamespaceAlias(): ?string;

    public function getTraitUsage(): string;
}