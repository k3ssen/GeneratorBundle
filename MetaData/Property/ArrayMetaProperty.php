<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class ArrayMetaProperty extends AbstractPrimitiveMetaProperty implements ArrayMetaPropertyInterface
{
    public const ORM_TYPE = Type::TARRAY;
    public const ORM_TYPE_ALIAS = 'arr';
    public const RETURN_TYPE = 'array';
}
