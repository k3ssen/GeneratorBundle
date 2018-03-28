<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class JsonMetaProperty extends AbstractPrimitiveMetaProperty implements JsonMetaPropertyInterface
{
    public const ORM_TYPE = Type::JSON;
    public const RETURN_TYPE = 'string';
}
