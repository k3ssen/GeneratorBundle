<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class DateIntervalMetaProperty extends AbstractPrimitiveMetaProperty
{
    public const ORM_TYPE = Type::DATEINTERVAL;
    public const RETURN_TYPE = '\DateInterval';
    public const ORM_TYPE_ALIAS = 'dint';
}
