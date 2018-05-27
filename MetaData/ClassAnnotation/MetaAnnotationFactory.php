<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\ClassAnnotation;

use K3ssen\GeneratorBundle\MetaData\ClassAnnotation\MetaAnnotationInterface;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

class MetaAnnotationFactory
{
    /** @var string */
    protected $metaClass;

    public function setMetaAnnotationClass(string $class)
    {
        $this->metaClass = $class;
    }

    public function createMetaAnnotation(MetaEntityInterface $metaEntity, $namespace, string $annotationName = null, array $annotationAttributes = []): MetaAnnotationInterface
    {
        return new $this->metaClass($metaEntity, $namespace, $annotationName, $annotationAttributes);
    }
}