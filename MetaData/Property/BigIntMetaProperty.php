<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class BigIntMetaProperty extends AbstractPrimitiveMetaProperty implements BigIntMetaPropertyInterface
{
    public const ORM_TYPE_ALIAS = 'bint';
    public const ORM_TYPE = Type::BIGINT;
    public const RETURN_TYPE = 'int';

    use LengthTrait;
}
