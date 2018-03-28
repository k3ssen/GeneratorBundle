<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Property;

interface PrimitiveMetaPropertyInterface extends MetaPropertyInterface
{
    public function isId(): ?bool;

    public function setId(bool $id);

    public function getDefault();

    public function setDefault($default);

    public function getColumnAnnotationOptions();
}