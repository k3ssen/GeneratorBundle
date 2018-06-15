<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Reader;

use Doctrine\ORM\Mapping\ClassMetadata;
use K3ssen\GeneratorBundle\MetaData\MetaEntityFactory;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\MetaData\Property\ManyToManyMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\ManyToOneMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyFactory;
use K3ssen\GeneratorBundle\MetaData\Property\OneToManyMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\OneToOneMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\RelationMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\PropertyAttribute\MetaAttributeFactory;
use K3ssen\GeneratorBundle\MetaData\Validation\MetaValidationFactory;

class ExistingEntityToMetaEntityReader
{
    /** @var MetaPropertyFactory */
    protected $metaPropertyFactory;

    /** @var MetaAttributeFactory */
    protected $metaAttributeFactory;

    /** @var MetaValidationFactory */
    protected $metaValidationFactory;

    /** @var MetaEntityFactory */
    protected $metaEntityFactory;

    /** @var bool */
    protected $useCustomRepository;

    public function __construct(
        ?bool $useCustomRepository,
        MetaPropertyFactory $metaPropertyFactory,
        MetaEntityFactory $metaEntityFactory,
        MetaAttributeFactory $metaAttributeFactory,
        MetaValidationFactory $metaValidationFactory
    ) {
        $this->useCustomRepository = $useCustomRepository;
        $this->metaEntityFactory = $metaEntityFactory;
        $this->metaPropertyFactory = $metaPropertyFactory;
        $this->metaAttributeFactory = $metaAttributeFactory;
        $this->metaValidationFactory = $metaValidationFactory;
    }

    public function extractExistingClassToMetaEntity(MetaEntityInterface $metaEntity)
    {
        if (!class_exists($metaEntity->getFullClassName())){
            throw new \InvalidArgumentException(sprintf('No existing class found for "%s"', $metaEntity->getFullClassName()));
        }
        $classMetadata = $this->metaEntityFactory->getDoctrineOrmClassMetadata($metaEntity->getFullClassName());

        //Loop over reflectionClass to maintain the right order.
        foreach ($classMetadata->getReflectionClass()->getProperties() as $reflectionProperty) {
            $name = $reflectionProperty->getName();
            //The id is generated without specifying it. Note: if a custom id is used, then this information will be overwritten.
            if ($name === 'id') {
                continue;
            }
            //Do not add trait-fields
            if ($this->isTraitField($name, $classMetadata)) {
                continue;
            }
            $type = $this->getTypeForFieldName($name, $classMetadata);
            if (!$type) {
                continue;
            }
            $metaProperty = $this->metaPropertyFactory->createMetaProperty($metaEntity, $type, $name);
            if (!$metaProperty) {
                continue;
            }
            $this->setAttributesByFieldMapping($metaProperty, $classMetadata);
            $this->addValidations($metaProperty, $classMetadata);
        }
    }

    protected function isTraitField($fieldName, ClassMetadata $classMetadata): bool
    {
        foreach ($classMetadata->getReflectionClass()->getTraits() as $trait) {
            foreach ($trait->getProperties() as $traitProperty) {
                if ($traitProperty->getName() === $fieldName) {
                    return true;
                }
            }
        }
        return false;
    }

    //TODO: set traits
//    protected function setTraits(MetaEntityInterface $metaEntity, ClassMetadata $classMetadata)
//    {
//        foreach ($classMetadata->getReflectionClass()->getTraits() as $trait) {
//            $metaEntity->addTrait(
//                (new MetaTraitInterface())
//                    ->setNamespace($interface->getNamespaceName())
//                    ->setName($interface->getShortName())
//            );
//        }
//    }
     //TODO: set interface + set parentClass
//    protected function setInterfaces(MetaEntityInterface $metaEntity, \ReflectionClass $reflectionClass)
//    {
//        foreach ($reflectionClass->getInterfaces() as $interface) {
//            $metaEntity->addInterface(
//                (new MetaInterface())
//                    ->setNamespace($interface->getNamespaceName())
//                    ->setName($interface->getShortName())
//            );
//        }
//    }

    protected function setAttributesByFieldMapping(MetaPropertyInterface $metaProperty, ClassMetadata $classMetadata)
    {
        $mapping = $classMetadata->fieldMappings[$metaProperty->getName()] ?? null;
        if (!$mapping and $metaProperty instanceof RelationMetaPropertyInterface) {
            $this->setAttributesByAssociationMapping($metaProperty, $classMetadata);
            return;
        }
        foreach ($mapping as $attributeName => $value) {
            if (!$metaProperty->hasAttribute($attributeName)) {
                $attribute = $this->metaAttributeFactory->createMetaAttribute($attributeName, ['value' => $value]);
                $metaProperty->addMetaAttribute($attribute);
            } else {
                $metaProperty->setAttribute($attributeName, $value);
            }
        }
    }

    protected function setAttributesByAssociationMapping(RelationMetaPropertyInterface $metaProperty, ClassMetadata $classMetadata)
    {
        $mapping = $classMetadata->associationMappings[$metaProperty->getName()] ?? null;
        if (!$mapping) {
            return;
        }
        $mapping['targetEntity'] = $this->metaEntityFactory->createByClassName($mapping['targetEntity']);
        if ($referencedColumnName = ($mapping['joinColumns']['referencedColumnName'] ?? null)) {
            $mapping['referencedColumnName'] = $referencedColumnName;
        }

        foreach ($mapping as $attributeName => $value) {
            if (!$metaProperty->hasAttribute($attributeName)) {
                $attribute = $this->metaAttributeFactory->createMetaAttribute($attributeName, ['value' => $value]);
                $metaProperty->addMetaAttribute($attribute);
            } else {
                $metaProperty->setAttribute($attributeName, $value);
            }
        }
    }

    protected function getTypeForFieldName($name, ClassMetadata $classMetadata)
    {
        $type = $classMetadata->fieldMappings[$name]['type'] ?? null;
        if (!$type) {
            $reflectionProperty = $classMetadata->getReflectionClass()->getProperty($name);
            $docComment = $reflectionProperty->getDocComment();
            if (strpos($docComment, 'ManyToOne') !== false) {
                return ManyToOneMetaPropertyInterface::ORM_TYPE;
            }
            if (strpos($docComment, 'OneToMany') !== false) {
                return OneToManyMetaPropertyInterface::ORM_TYPE;
            }
            if (strpos($docComment, 'ManyToMany') !== false) {
                return ManyToManyMetaPropertyInterface::ORM_TYPE;
            }
            if (strpos($docComment, 'OneToOne') !== false) {
                return OneToOneMetaPropertyInterface::ORM_TYPE;
            }
        }
        return $type;
    }

    protected function addValidations(MetaPropertyInterface $metaProperty, ClassMetadata $classMetadata)
    {
        $reflectionProperty = $classMetadata->getReflectionClass()->getProperty($metaProperty->getName());
        $docComment = $reflectionProperty->getDocComment();
        if (preg_match_all('/@Assert\\\\(\w+)(\(.*\))?/', $docComment, $matches) > 0) {
            foreach ($matches[1] as $index => $validationName) {
                $optionsString = $matches[2][$index];
                $options = [];
                if ($optionsString) {
                    //For for things like choices={"a","b","c"} OR message="somemessage" OR required=true
                    preg_match_all('/(\w+)=((\w+)|("[^"]+")|(\{[^}]+\}))/', substr($optionsString, 1, -1), $optionMatches);
                    foreach ($optionMatches[1] as $optionIndex => $optionName) {
                        $options[$optionName] = trim($optionMatches[2][$optionIndex], '"');
                    }
                }
                $this->metaValidationFactory->createMetaValidation($metaProperty, $validationName, $options);
            }
        }
    }
}