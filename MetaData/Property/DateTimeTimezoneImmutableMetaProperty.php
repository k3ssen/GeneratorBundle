<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

class DateTimeTimezoneImmutableMetaProperty extends AbstractPrimitiveMetaProperty implements DateTimeTimezoneImmutableMetaPropertyInterface
{
    public const ORM_TYPE = 'datetimetz_immutable'; //Type::DATETIMETZ_IMMUTABLE; not supported in doctrine/dbal <v2.6
    public const RETURN_TYPE = '\DateTimeImmutable';
    public const ORM_TYPE_ALIAS = 'dttzim';
}
