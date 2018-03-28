<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Inflector\Inflector;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

class OneToManyMetaProperty extends AbstractRelationMetaProperty implements OneToManyMetaPropertyInterface
{
    public const ORM_TYPE_ALIAS = 'o2m';
    public const RETURN_TYPE = 'Collection';

    public function __construct(MetaEntityInterface $metaEntity, ArrayCollection $metaAttributes, string $name)
    {
        parent::__construct($metaEntity, $metaAttributes, $name);
        $this->setMappedBy(lcfirst($metaEntity->getName()));
        $this->setOrphanRemoval(false);

        $metaEntity->addUsage('Doctrine\Common\Collections\Collection');
        $metaEntity->addUsage('Doctrine\Common\Collections\ArrayCollection');
    }

    public function setInversedBy(?string $inversedBy): RelationMetaPropertyInterface
    {
        throw new \RuntimeException(sprintf('Cannot call setInversedBy on "%s"; A OneToMany property always is the inversed side', static::class));
    }

    public function setNullable(?bool $nullable)
    {
        if ($nullable === false) {
            throw new \BadMethodCallException('Setting nullable to false on OneToMany is not possible.');
        }
        return parent::setNullable($nullable);
    }

    public function getAnnotationLines(): array
    {
        $OneToManyOptions = 'targetEntity="'.$this->getTargetEntity()->getFullClassName().'"';
        $OneToManyOptions .= ', mappedBy="'.$this->getMappedBy().'"';
        $OneToManyOptions .= $this->getOrphanRemoval() ? ', orphanRemoval=true' : '';
        //TODO: what about cascade delete?
        $OneToManyOptions .= ', cascade={"persist"}';

        $annotationLines = [
            '@ORM\OneToMany('.$OneToManyOptions.')',
        ];
        return array_merge($annotationLines, parent::getAnnotationLines());
    }
}
