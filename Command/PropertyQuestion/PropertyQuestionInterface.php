<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\PropertyQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyInterface;

interface PropertyQuestionInterface
{
    public function doQuestion(CommandInfo $commandInfo, MetaPropertyInterface $metaProperty);
}