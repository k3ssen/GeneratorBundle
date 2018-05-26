<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\EntityQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\Reader\BundleProvider;
use Symfony\Component\Console\Question\Question;

class BundleQuestion implements EntityQuestionInterface
{
    public const PRIORITY = 80;

    /** @var BundleProvider */
    protected $bundleProvider;
    /** @var bool */
    protected $askBundle;
    /** @var string */
    protected $defaultBundleName;

    public function __construct(BundleProvider $bundleProvider, bool $askBundle, string $defaultBundle = null)
    {
        $this->bundleProvider = $bundleProvider;
        $this->askBundle = $askBundle;
        $this->defaultBundleName = $defaultBundle;
    }

    public function addActions(CommandInfo $commandInfo, array &$actions)
    {
        if ($this->askBundle === false) {
            return;
        }
        $actions['Edit bundle'] = function() use($commandInfo) {
            $this->doQuestion($commandInfo);
        };
    }

    public function doQuestion(CommandInfo $commandInfo)
    {
        if ($this->askBundle === false) {
            $bundleNamespace = $this->bundleProvider->getBundleNamespaceByName($this->defaultBundleName);

            $commandInfo->getMetaEntity()->setBundleNamespace($bundleNamespace);
            return;
        }
        $options = $this->bundleProvider->getBundleNameOptions();
        $commandInfo->getIo()->listing($options);
        $question = new Question('Bundle (optional)', $commandInfo->getMetaEntity()->getBundleName());
        $question->setAutocompleterValues($options);
        $bundleChoice = $commandInfo->getIo()->askQuestion($question);

        $bundleNamespace = $bundleChoice ? $this->bundleProvider->getBundleNamespaceByName($bundleChoice) : null;

        $commandInfo->getMetaEntity()->setBundleNamespace($bundleNamespace);
    }
}