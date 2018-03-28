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

    public function __construct(BundleProvider $bundleProvider)
    {
        $this->bundleProvider = $bundleProvider;
    }

    public function addActions(CommandInfo $commandInfo, array &$actions) {
        $actions['Edit bundle'] = function() use($commandInfo) {
            $this->doQuestion($commandInfo);
        };
    }

    public function doQuestion(CommandInfo $commandInfo)
    {
        $options = $this->bundleProvider->getBundleNameOptions();
        $commandInfo->getIo()->listing($options);
        $question = new Question('Bundle (optional)', $commandInfo->getMetaEntity()->getBundleName());
        $question->setAutocompleterValues($options);
        $bundleChoice = $commandInfo->getIo()->askQuestion($question);

        $bundleNamespace = $bundleChoice ? $this->bundleProvider->getBundleNamespaceByName($bundleChoice) : null;

        $commandInfo->getMetaEntity()->setBundleNamespace($bundleNamespace);
    }
}