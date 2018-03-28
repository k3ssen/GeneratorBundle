<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\Common\Inflector\Inflector;

abstract class AbstractPrimitiveMetaProperty extends AbstractMetaProperty implements PrimitiveMetaPropertyInterface
{
    protected $default;

    public function isId(): ?bool
    {
        return $this->getAttribute('id');
    }

    public function setId(bool $id)
    {
        return $this->setAttribute('id', $id);
    }

    public function getDefault()
    {
        return $this->getAttribute('default');
    }

    public function setDefault($default)
    {
        return $this->setAttribute('default', $default);
    }

    public function getAnnotationLines(): array
    {
        $annotationLines = [
            '@ORM\Column('.$this->getColumnAnnotationOptions().')'
        ];
        if ($this->isId()) {
            $annotationLines[] = '@ORM\Id';
        }
        return array_merge($annotationLines, parent::getAnnotationLines());
    }

    public function getColumnAnnotationOptions()
    {
        $optionsString = 'name="'.Inflector::tableize($this->getName()).'"';
        $optionsString .= ', type="'.$this->getOrmType().'"';
        $optionsString .= $this->isUnique() ? ', unique=true' : '';
        $optionsString .= $this->isNullable() ? ', nullable=true' : '';

        return $optionsString;
    }
}