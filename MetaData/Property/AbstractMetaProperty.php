<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\Common\Util\Inflector;
use Doctrine\Common\Collections\ArrayCollection;
use K3ssen\GeneratorBundle\MetaData\MetaAttributeInterface;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\MetaData\MetaValidationInterface;

abstract class AbstractMetaProperty implements MetaPropertyInterface
{
    /** @var MetaEntityInterface */
    protected $metaEntity;

    /** @var MetaValidationInterface[]|ArrayCollection */
    protected $validations;

    /** @var MetaAttributeInterface[]|ArrayCollection */
    protected $metaAttributes;

    public function __construct(MetaEntityInterface $metaEntity, ArrayCollection $metaAttributes, string $name)
    {
        $this->metaAttributes = $metaAttributes;
        $this->setMetaEntity($metaEntity);
        $this->setName($name);
        $this->validations = new ArrayCollection();

        $metaEntity->addProperty($this);
    }

    public static function getReturnType(): string
    {
        if (defined('static::RETURN_TYPE')) {
            return constant('static::RETURN_TYPE');
        }
        throw new \RuntimeException('Expected public constant "RETURN_TYPE" to be defined in class "%s", but none found', static::class);
    }

    public static function getOrmType(): string
    {
        if (defined('static::ORM_TYPE')) {
            return constant('static::ORM_TYPE');
        }
        throw new \RuntimeException('Expected public constant "ORM_TYPE" to be defined in class "%s", but none found', static::class);
    }

    public static function getOrmTypeAlias(): string
    {
        if (defined('static::ORM_TYPE_ALIAS')) {
            return constant('static::ORM_TYPE_ALIAS');
        }
        return static::getOrmType();
    }

    public function getMetaEntity(): MetaEntityInterface
    {
        return $this->metaEntity;
    }

    public function setMetaEntity(MetaEntityInterface $metaEntity)
    {
        $this->metaEntity = $metaEntity;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function setName(string $name)
    {
        return $this->setAttribute('name', lcfirst(Inflector::classify($name)));
    }

    public function isNullable(): ?bool
    {
        return $this->getAttribute('nullable');
    }

    public function setNullable(?bool $nullable)
    {
        return $this->setAttribute('nullable', $nullable);
    }

    public function isUnique(): ?bool
    {
        return $this->getAttribute('unique');
    }

    public function setUnique(?bool $unique)
    {
        return $this->setAttribute('unique', $unique);
    }

    /** @return ArrayCollection|MetaValidationInterface[] */
    public function getValidations(): ArrayCollection
    {
        return $this->validations;
    }

    public function setValidations(ArrayCollection $validations)
    {
        $this->validations = $validations;
        return $this;
    }

    public function addValidation(MetaValidationInterface $validation)
    {
        if (!$this->getValidations()->contains($validation)) {
            $this->getValidations()->add($validation);
            $validation->setMetaProperty($this);
        }
        return $this;
    }

    public function removeValidation(MetaValidationInterface $validation)
    {
        if ($this->getValidations()->contains($validation)) {
            $this->getValidations()->removeElement($validation);
        }
        return $this;
    }

    public function isHasValidation(): bool
    {
        return !$this->getValidations()->isEmpty();
    }

    /**
     * @return array|ArrayCollection|MetaAttributeInterface[]
     */
    public function getMetaAttributes(): ArrayCollection
    {
        return $this->metaAttributes;
    }

    public function addMetaAttribute(MetaAttributeInterface $metaAttribute)
    {
        if (!$this->getMetaAttributes()->contains($metaAttribute)) {
            $this->getMetaAttributes()->set($metaAttribute->getName(), $metaAttribute);
            $metaAttribute->setMetaProperty($this);
        }
        return $this;
    }

    public function removeMetaAttribute(MetaAttributeInterface $metaAttribute)
    {
        if (!$this->getMetaAttributes()->contains($metaAttribute)) {
            $this->getMetaAttributes()->removeElement($metaAttribute);
        }
        return $this;
    }

    public function getMetaAttribute($name): MetaAttributeInterface
    {
        $metaAttribute = $this->getMetaAttributes()->get($name);
        if ($metaAttribute === null) {
            throw new \InvalidArgumentException(sprintf('No attribute "%s" has been defined for this metaProperty', $name));
        }
        return $metaAttribute;
    }

    public function hasAttribute($name): bool
    {
        return $this->getMetaAttributes()->containsKey($name);
    }

    public function getAttribute($name)
    {
        return $this->getMetaAttribute($name)->getValue();
    }

    public function setAttribute($name, $value)
    {
        $this->getMetaAttribute($name)->setValue($value);
        return $this;
    }

    public function getAnnotationLines(): array
    {
        $annotationLines = [];
        foreach ($this->getValidations() as $validation) {
            $annotationLines[] = $validation->getAnnotationFormatted();
        }
        return $annotationLines;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
