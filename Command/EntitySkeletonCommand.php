<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class EntitySkeletonCommand extends Command
{
    protected static $defaultName = 'generator:create:skeleton';

    protected $projectDir;

    public function __construct(?string $name = null, string $projectDir)
    {
        parent::__construct($name);
        $this->projectDir = $projectDir;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add template files to quickly override the skeleton')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrite files only if newer')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $overwrite = $input->getOption('overwrite') ?? false;

        $fs = new Filesystem();

        $originDir = __DIR__ . '/../Resources/skeleton_overrides/';
        $targetDir = $this->projectDir  . '/templates/bundles/GeneratorBundle/skeleton/';

        $fs->mirror($originDir, $targetDir,null, ['override' => $overwrite]);

        (new SymfonyStyle($input, $output))->success(sprintf('Created files in %s', $targetDir));
    }
}
