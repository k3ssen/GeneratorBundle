<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Inflector\Inflector;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

class ManyToOneMetaProperty extends AbstractRelationMetaProperty implements ManyToOneMetaPropertyInterface
{
    public const ORM_TYPE_ALIAS = 'm2o';
    public const RETURN_TYPE = '\stdClass'; //Note that this class is an exception in which we actually want to return the targetEntity as returnType

    public function __construct(MetaEntityInterface $metaEntity, ArrayCollection $metaAttributes, string $name)
    {
        parent::__construct($metaEntity, $metaAttributes, $name);
        $this->setInversedBy(lcfirst($metaEntity->getName()));
    }

    public static function getReturnType(self $property = null): string
    {
        if ($property) {
            return $property->getTargetEntity()->getName();
        }
        return parent::getReturnType();
    }

    public function setMappedBy(?string $mappedBy): RelationMetaPropertyInterface
    {
        throw new \RuntimeException(sprintf('Cannot call setMappedBy on "%s"; A ManyToOne property always is the mapping side', static::class));
    }

    public function getAnnotationLines(): array
    {
        $manyToOneOptions = 'targetEntity="'.$this->getTargetEntity()->getFullClassName().'"';
        $manyToOneOptions .= $this->getInversedBy() ? ', inversedBy="'.$this->getInversedBy().'"' : '';

        $joinColumnOptions = 'name="'. Inflector::tableize($this->getName()) . ($this->getReferencedColumnName() === 'id' ? '_id"': '');
        $joinColumnOptions .= ', referencedColumnName="'.$this->getReferencedColumnName().'"';
        $joinColumnOptions .= $this->isNullable() ? ', nullable=true' : ', nullable=false';

        $annotationLines = [
            '@ORM\ManyToOne('.$manyToOneOptions.', cascade={"persist"})',
            '@ORM\JoinColumn('.$joinColumnOptions.')',
        ];
        return array_merge($annotationLines, parent::getAnnotationLines());
    }
}
