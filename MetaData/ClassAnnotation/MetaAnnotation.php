<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\ClassAnnotation;

use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

class MetaAnnotation implements MetaAnnotationInterface
{
    /**
     * @var MetaEntityInterface
     */
    protected $metaEntity;
    /**
     * @var string|array
     */
    protected $namespace;
    /**
     * @var string
     */
    protected $annotationName;
    /**
     * @var array
     */
    protected $annotationAttributes;

    /**
     * @param MetaEntityInterface $metaEntity
     * @param string|array $namespace the namespace to be used in the entity. Can be an array to provide an alias
     *      string-format= namespace / array-format= [namespace=>alias]
     * @param string|null $annotationName the name-part to be used in the dockblock.
     *      (eg in '@ORM\Entity()', the 'ORM\Entity' is considered the name.
     *      If no annotationName is provided, the namespace (or namespace alias) will be used.
     */
    public function __construct(MetaEntityInterface $metaEntity, $namespace, string $annotationName = null, array $annotationAttributes = [])
    {
        $this->metaEntity = $metaEntity;
        $this->setNamespace($namespace);
        $this->annotationName = $annotationName;
        $this->annotationAttributes = $annotationAttributes;

        $metaEntity->addUsage($this->getNamespace(), $this->getNamespaceAlias());
    }

    protected function setNamespace($namespace)
    {
        if (empty($namespace)) {
            throw new \InvalidArgumentException('Namespace for MetaAnnotation cannot be empty.');
        }
        if (is_array($namespace)) {
            if (count($namespace) > 1) {
                throw new \InvalidArgumentException('Only one namespace can be provided for a MetaAnnotation');
            }
            if (is_numeric(current(array_keys($namespace)))) {
                throw new \InvalidArgumentException('When providing namespace as an array, the key must contain the namespace (string), but numeric key was found.');
            }
        }
        $this->namespace = $namespace;
    }

    public function getMetaEntity(): MetaEntityInterface
    {
        return $this->getMetaEntity();
    }

    public function getNamespace(): string
    {
        if (is_array($this->namespace)) {
            reset($this->namespace);
            return current(array_keys($this->namespace));
        }
        return $this->namespace;
    }

    public function getNamespaceAlias(): ?string
    {
        if (is_array($this->namespace)) {
            return reset($this->namespace) ?: null;
        }
        return $this->namespace;
    }

    public function getAnnotationName(): string
    {
        return $this->annotationName
            ?: $this->getNamespaceAlias()
            ?: (new \ReflectionClass($this->getNamespace()))->getShortName()
        ;
    }

    public function getAnnotationAttributes(): array
    {
        return $this->annotationAttributes;
    }

    public function addAnnotationAttribute(string $name, $value)
    {
        $this->annotationAttributes[$name] = $value;
        return $this;
    }
}