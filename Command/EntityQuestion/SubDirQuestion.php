<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\EntityQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;

class SubDirQuestion implements EntityQuestionInterface
{
    public const PRIORITY = 70;

    public function addActions(CommandInfo $commandInfo, array &$actions) {
        $actions['Edit sub directory'] = function() use($commandInfo) {
            $this->doQuestion($commandInfo);
        };
    }

    public function doQuestion(CommandInfo $commandInfo)
    {
        $subDir = $commandInfo->getIo()->ask(
            'Sub directory (optional)',
            $commandInfo->getMetaEntity()->getSubDir()
        );
        $commandInfo->getMetaEntity()->setSubDir($subDir);
    }
}