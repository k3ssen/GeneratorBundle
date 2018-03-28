<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\EntityAnnotation;


use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

class OrmTableAnnotation implements AnnotationInterface
{
    protected $metaEntity;

    public function __construct(MetaEntityInterface $metaEntity)
    {
        $this->metaEntity = $metaEntity;
        $metaEntity->addUsage('Doctrine\ORM\Mapping', 'ORM');
    }

    public function getNamespace(): string
    {
        return 'ORM\Table';
    }

    public function getAnnotationProperties(): ?array
    {
       return [
           'name' => $this->metaEntity->getTableName(),
       ];
    }
}