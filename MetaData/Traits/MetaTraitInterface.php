<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Traits;


use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

interface MetaTraitInterface
{
    public function __construct(MetaEntityInterface $metaEntity);

    public function getName(): string;

    public function getNamespace(): string;
}