<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class DateTimeImmutableMetaProperty extends AbstractPrimitiveMetaProperty implements DateTimeImmutableMetaPropertyInterface
{
    public const ORM_TYPE = 'datetime_immutable'; //Type::DATETIME_IMMUTABLE; not supported in doctrine/dbal <v2.6
    public const RETURN_TYPE = '\DateTimeImmutable';
    public const ORM_TYPE_ALIAS = 'dtim';
}
