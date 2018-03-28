<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class ObjectMetaProperty extends AbstractPrimitiveMetaProperty implements ObjectMetaPropertyInterface
{
    public const ORM_TYPE = Type::OBJECT;
    public const ORM_TYPE_ALIAS = 'obj';
    public const RETURN_TYPE = '\stdClass'; //TODO: what to use? it's very possible that stdClass isn't correct.
}
