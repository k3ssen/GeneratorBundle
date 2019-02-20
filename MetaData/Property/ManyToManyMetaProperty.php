<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Inflector\Inflector;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

class ManyToManyMetaProperty extends AbstractRelationMetaProperty implements ManyToManyMetaPropertyInterface
{
    public const ORM_TYPE_ALIAS = 'm2m';
    public const RETURN_TYPE = 'Collection';

    public function __construct(MetaEntityInterface $metaEntity, ArrayCollection $metaAttributes, string $name)
    {
        parent::__construct($metaEntity, $metaAttributes, $name);
        $this->setInversedBy(Inflector::pluralize(lcfirst($metaEntity->getName())));

        $metaEntity->addUsage('Doctrine\Common\Collections\Collection');
        $metaEntity->addUsage('Doctrine\Common\Collections\ArrayCollection');
    }

    public function setNullable(?bool $nullable)
    {
        if ($nullable === false) {
            throw new \BadMethodCallException('Setting nullable to false on ManyToMany is not possible.');
        }
        return parent::setNullable($nullable);
    }

    public function getAnnotationLines(): array
    {
        $manyToManyOptions = 'targetEntity="'.$this->getTargetEntity()->getFullClassName().'"';
        $manyToManyOptions .= $this->getInversedBy() ? ', inversedBy="'.$this->getInversedBy().'"' : '';
        $manyToManyOptions .= $this->getMappedBy() ? ', mappedBy="'.$this->getMappedBy().'"' : '';
        $manyToManyOptions .= $this->getOrphanRemoval() ? ', orphanRemoval=true' : '';
        $manyToManyOptions .= ', cascade={"persist"}';

        $annotationLines =  [
            '@ORM\ManyToMany('.$manyToManyOptions.')',
        ];

        if (!$this->getMappedBy()) {
            $entityId = $this->getMetaEntity()->getIdProperty() ? $this->getMetaEntity()->getIdProperty()->getName() : 'id';

            $tableName = Inflector::pluralize(Inflector::tableize($this->getMetaEntity()->getName())).'_'.Inflector::pluralize(Inflector::tableize($this->getTargetEntity()->getName()));
            $annotationLines[] = '@ORM\JoinTable(name="'.$tableName.'",';
            $annotationLines[] = '  joinColumns={';
            $annotationLines[] = '    @ORM\JoinColumn(name="'.Inflector::tableize($this->getMetaEntity()->getName()).'_'.$entityId.'", referencedColumnName="'.$entityId.'", onDelete="CASCADE")';
            $annotationLines[] = '  },';
            $annotationLines[] = '  inverseJoinColumns={';
            $annotationLines[] = '    @ORM\JoinColumn(name="'.Inflector::tableize($this->getTargetEntity()->getName()).'_'.$this->getReferencedColumnName().'" , referencedColumnName="'.$this->getReferencedColumnName().'", onDelete="CASCADE")';
            $annotationLines[] = '  }';
            $annotationLines[] = ')';
        }

        return array_merge($annotationLines, parent::getAnnotationLines());
    }
}
