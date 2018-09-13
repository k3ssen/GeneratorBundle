<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\MetaData\PropertyAttribute\MetaAttributeFactory;
use K3ssen\GeneratorBundle\MetaData\PropertyAttribute\MetaAttributeInterface;

class MetaPropertyFactory
{
    /** @var array */
    protected $metaPropertyClasses = [];

    /** @var MetaAttributeFactory */
    protected $metaAttributeFactory;

    public function __construct(MetaAttributeFactory $metaAttributeFactory)
    {
        $this->metaAttributeFactory = $metaAttributeFactory;
    }

    public function addMetaPropertyClass(string $class)
    {
        $ormType = call_user_func([$class, 'getOrmType']);
        if (!array_key_exists($ormType, $this->metaPropertyClasses) && Type::hasType($ormType)) {
            $this->metaPropertyClasses[$ormType] = $class;
        }
    }

    public function getTypes()
    {
        return $this->metaPropertyClasses;
    }

    public static function getInversedType($type): string
    {
        switch ($type) {
            case ManyToOneMetaPropertyInterface::ORM_TYPE:
                return OneToManyMetaPropertyInterface::ORM_TYPE;
            case OneToManyMetaPropertyInterface::ORM_TYPE:
                return ManyToOneMetaPropertyInterface::ORM_TYPE;
            case ManyToManyMetaPropertyInterface::ORM_TYPE:
            case OneToOneMetaPropertyInterface::ORM_TYPE:
                return $type;
            default:
                throw new \InvalidArgumentException(sprintf('Type "%s" has no inversed type.', $type));
        }
    }

    public function getAliasedTypeOptions(): array
    {
        $aliasedTypes = [];
        foreach ($this->metaPropertyClasses as $ormType => $class) {
            $aliasedTypes[call_user_func([$class, 'getOrmTypeAlias'])] = $ormType;
        }
        return $aliasedTypes;
    }

    public function createMetaProperty(MetaEntityInterface $metaEntity, string $type, string $name): ?MetaPropertyInterface
    {
        if (!array_key_exists($type, $this->getTypes())) {
            return null;
        }
        /** @var MetaPropertyInterface $typeClass */
        $typeClass = $this->getTypes()[$type];
        /** @var MetaAttributeInterface[]|ArrayCollection $metaAttributes */
        $metaAttributes = new ArrayCollection();

        foreach ($this->metaAttributeFactory->getAttributesList() as $attributeName => $attributeInfo) {
            $classes = $attributeInfo['meta_properties'] ?? [];
            if (is_string($classes)) {
                $classes = [$classes];
            }
            if (empty($classes)) {
                $metaAttributes->set($attributeName, $this->metaAttributeFactory->createMetaAttribute($attributeName, $attributeInfo));
                continue;
            }
            foreach ($classes as $class) {
                if (is_a($typeClass, $class, true)) {
                    $metaAttributes->set($attributeName, $this->metaAttributeFactory->createMetaAttribute($attributeName, $attributeInfo));
                }
            }
        }
        $metaProperty = new $typeClass($metaEntity, $metaAttributes, $name);
        foreach ($metaAttributes as $metaAttribute) {
            $metaAttribute->setMetaProperty($metaProperty);
        }
        return $metaProperty;
    }
}