<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\EntityQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;

interface EntityQuestionInterface
{
    public function addActions(CommandInfo $commandInfo, array &$actions);
    public function doQuestion(CommandInfo $commandInfo);
}