<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

class DateImmutableMetaProperty extends AbstractPrimitiveMetaProperty implements DateImmutableMetaPropertyInterface
{
    public const ORM_TYPE = 'date_immutable'; //Type::DATE_IMMUTABLE; not supported in doctrine/dbal <v2.6
    public const RETURN_TYPE = '\DateTimeImmutable';
    public const ORM_TYPE_ALIAS = 'dim';
}
