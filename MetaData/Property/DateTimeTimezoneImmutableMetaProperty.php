<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class DateTimeTimezoneImmutableMetaProperty extends AbstractPrimitiveMetaProperty implements DateTimeTimezoneImmutableMetaPropertyInterface
{
    public const ORM_TYPE = Type::DATETIMETZ_IMMUTABLE;
    public const RETURN_TYPE = '\DateTimeImmutable';
    public const ORM_TYPE_ALIAS = 'dttzim';
}
