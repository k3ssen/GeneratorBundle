<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

interface ManyToOneMetaPropertyInterface extends RelationMetaPropertyInterface
{
    public const ORM_TYPE = 'ManyToOne';
}
