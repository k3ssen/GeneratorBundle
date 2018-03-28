<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class DateMetaProperty extends AbstractPrimitiveMetaProperty
{
    public const ORM_TYPE = Type::DATE;
    public const RETURN_TYPE = '\DateTime';
    public const ORM_TYPE_ALIAS = 'date';
}
