<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Traits;

use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

class MetaTrait implements MetaTraitInterface
{
    /**
     * @var MetaEntityInterface
     */
    protected $metaEntity;
    /**
     * @var array|string
     */
    protected $namespace;
    /**
     * @var null|string
     */
    protected $traitUsage;

    /**
     * @param MetaEntityInterface $metaEntity
     * @param string|array $namespace the namespace to be used in the entity. Can be an array to provide an alias
     *      string-format= namespace / array-format= [namespace=>alias]
     *      Note that the namespace should be the fullClassName if no traitUsage is proved
     * @param string|null $traitUsage the string to be used in the 'use Trait;' part.
     *      if no name is provided, the name is derived from the namespace
     */
    public function __construct(MetaEntityInterface $metaEntity, $namespace, string $traitUsage = null)
    {
        $this->metaEntity = $metaEntity;
        $this->setNamespace($namespace);
        $this->traitUsage = $traitUsage;

        $metaEntity->addUsage($this->getNamespace(), $this->getNamespaceAlias());
    }

    protected function setNamespace($namespace)
    {
        if (empty($namespace)) {
            throw new \InvalidArgumentException('Namespace for MetaTrait cannot be empty.');
        }
        if (is_array($namespace)) {
            if (count($namespace) > 1) {
                throw new \InvalidArgumentException('Only one namespace can be provided for a MetaTrait');
            }
            if (is_numeric(current(array_keys($namespace)))) {
                throw new \InvalidArgumentException('When providing namespace as an array, the key must contain the namespace (string), but numeric key was found.');
            }
        }
        $this->namespace = $namespace;
    }

    public function getMetaEntity(): MetaEntityInterface
    {
        return $this->metaEntity;
    }

    public function getNamespace(): string
    {
        if (is_array($this->namespace)) {
            reset($this->namespace);
            return key($this->namespace);
        }
        return $this->namespace;
    }

    public function getNamespaceAlias(): ?string
    {
        if (is_array($this->namespace)) {
            return reset($this->namespace) ?: null;
        }
        return null;
    }

    public function getTraitUsage(): string
    {
        return $this->traitUsage
            ?: $this->getNamespaceAlias()
            ?: (new \ReflectionClass($this->getNamespace()))->getShortName()
        ;
    }
}