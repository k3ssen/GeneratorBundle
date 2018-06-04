<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Interfaces;

use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

class MetaInterfaceFactory
{
    /** @var string */
    protected $metaInterfaceClass;

    public function setMetaInterfaceClass(string $class)
    {
        $this->metaInterfaceClass = $class;
    }

    public function createMetaInterface(MetaEntityInterface $metaEntity, string $namespace, string $interfaceUsage = null): MetaInterfaceInterface
    {
        return new $this->metaInterfaceClass($metaEntity, $namespace, $interfaceUsage);
    }
}