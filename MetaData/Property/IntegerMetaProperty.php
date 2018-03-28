<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class IntegerMetaProperty extends AbstractPrimitiveMetaProperty implements IntegerMetaPropertyInterface
{
    public const ORM_TYPE = Type::INTEGER;
    public const RETURN_TYPE = 'int';
    public const ORM_TYPE_ALIAS = 'int';

    use LengthTrait;
}
