<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\EntityQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;

class SubdirectoryQuestion implements EntityQuestionInterface
{
    public const PRIORITY = 70;
    /**
     * @var bool
     */
    protected $askSubdirectory;
    /**
     * @var string
     */
    protected $defaultSubdirectory;

    public function __construct(bool $askSubdirectory, string $defaultSubdirectory = null)
    {
        $this->askSubdirectory = $askSubdirectory;
        $this->defaultSubdirectory = $defaultSubdirectory;
    }

    public function addActions(CommandInfo $commandInfo, array &$actions) {
        if ($this->askSubdirectory) {
            $actions['Edit subdirectory'] = function () use ($commandInfo) {
                $this->doQuestion($commandInfo);
            };
        }
    }

    public function doQuestion(CommandInfo $commandInfo)
    {
        if ($this->askSubdirectory === false) {
            $commandInfo->getMetaEntity()->setSubDir($this->defaultSubdirectory);
            return;
        }
        $subDir = $commandInfo->getIo()->ask(
            'Subdirectory (optional)',
            $commandInfo->getMetaEntity()->getSubDir()
        );
        $commandInfo->getMetaEntity()->setSubDir($subDir);
    }
}