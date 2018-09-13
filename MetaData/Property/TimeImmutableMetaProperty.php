<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

class TimeImmutableMetaProperty extends AbstractPrimitiveMetaProperty implements TimeMetaPropertyInterface
{
    public const ORM_TYPE = 'time_immutable'; //Type::TIME_IMMUTABLE; not supported in doctrine/dbal <v2.6
    public const ORM_TYPE_ALIAS = 'tim';
    public const RETURN_TYPE = '\DateTimeImmutable';
}
