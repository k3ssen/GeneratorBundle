<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class SimpleArrayMetaProperty extends AbstractPrimitiveMetaProperty implements SimpleArrayMetaPropertyInterface
{
    public const ORM_TYPE = Type::SIMPLE_ARRAY;
    public const ORM_TYPE_ALIAS = 'sarr';
    public const RETURN_TYPE = 'array';
}
