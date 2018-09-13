<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

class JsonMetaProperty extends AbstractPrimitiveMetaProperty implements JsonMetaPropertyInterface
{
    public const ORM_TYPE = 'json'; //Type::JSON; not supported in doctrine/dbal <v2.6
    public const RETURN_TYPE = 'string';
}
