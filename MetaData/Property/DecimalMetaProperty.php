<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

use Doctrine\DBAL\Types\Type;

class DecimalMetaProperty extends AbstractPrimitiveMetaProperty implements DecimalMetaPropertyInterface
{
    public const ORM_TYPE = Type::DECIMAL;
    public const RETURN_TYPE = 'string';
    public const ORM_TYPE_ALIAS = 'dec';

    /** @var int */
    protected $precision;

    /** @var int */
    protected $scale;

    public function getPrecision(): ?int
    {
        return $this->getAttribute('precision');
    }

    public function setPrecision(?int $precision)
    {
        return $this->setAttribute('precision', $precision);
    }

    public function getScale(): ?int
    {
        return $this->getAttribute('scale');
    }

    public function setScale(?int $scale)
    {
        return $this->setAttribute('scale', $scale);
    }

    public function getColumnAnnotationOptions()
    {
        $optionsString = parent::getColumnAnnotationOptions();
        $optionsString .= $this->getPrecision() ? ', length='.$this->getPrecision() : '';
        $optionsString .= $this->getScale() ? ', scale='.$this->getScale() : '';

        return $optionsString;
    }
}
