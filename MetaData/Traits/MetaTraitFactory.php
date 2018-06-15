<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Traits;

use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

class MetaTraitFactory
{
    /** @var string */
    protected $metaTraitClass;

    public function setMetaTraitClass(string $class)
    {
        $this->metaTraitClass = $class;
    }

    public function createMetaTrait(MetaEntityInterface $metaEntity, $namespace, string $traitUsage = null): MetaTraitInterface
    {
        return new $this->metaTraitClass($metaEntity, $namespace, $traitUsage);
    }
}