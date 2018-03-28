<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class StringMetaProperty extends AbstractPrimitiveMetaProperty implements StringMetaPropertyInterface
{
    public const ORM_TYPE = Type::STRING;
    public const ORM_TYPE_ALIAS = 'str';
    public const RETURN_TYPE = 'string';

    use LengthTrait;
}
