<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class DateTimeTimezoneMetaProperty extends AbstractPrimitiveMetaProperty implements DateTimeTimezoneMetaPropertyInterface
{
    public const ORM_TYPE = Type::DATETIMETZ;
    public const RETURN_TYPE = '\DateTime';
    public const ORM_TYPE_ALIAS = 'dttz';
}
