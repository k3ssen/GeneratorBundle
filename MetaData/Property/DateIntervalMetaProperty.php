<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

class DateIntervalMetaProperty extends AbstractPrimitiveMetaProperty
{
    public const ORM_TYPE = 'dateinterval'; //Type::DATEINTERVAL; not supported in doctrine/dbal <v2.6
    public const RETURN_TYPE = '\DateInterval';
    public const ORM_TYPE_ALIAS = 'dint';
}
