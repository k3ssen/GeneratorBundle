<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class TimeImmutableMetaProperty extends AbstractPrimitiveMetaProperty implements TimeMetaPropertyInterface
{
    public const ORM_TYPE = Type::TIME_IMMUTABLE;
    public const ORM_TYPE_ALIAS = 'ti';
    public const RETURN_TYPE = '\DateTimeImmutable';
}
