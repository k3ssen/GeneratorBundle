<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

trait LengthTrait
{
    public function getLength(): ?int
    {
        return $this->getAttribute('length');
    }

    public function setLength(?int $length)
    {
        return $this->setAttribute('length', $length);
    }

    public function getColumnAnnotationOptions()
    {
        $optionsString = parent::getColumnAnnotationOptions();
        $optionsString .= $this->getLength() ? ', length='.$this->getLength() : '';

        return $optionsString;
    }
}
