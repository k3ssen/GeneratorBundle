<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class TemplatesCommand extends Command
{
    protected static $defaultName = 'generator:templates';

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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $fs = new Filesystem();

        $originDir = __DIR__ . '/../Resources/views';
        $targetDir = $this->projectDir  . '/templates/bundles/GeneratorBundle/';

        $finder = new Finder();
        $finder->files()->in($originDir);

        foreach ($finder as $file) {
            $relativePathname = $file->getRelativePathname();
            $content = "{% extends '@!Generator/".$relativePathname."' %}";
            if ($file->getFilename()[0] === '_') {
                $content = "{% use '@!Generator/".$relativePathname."' %}";
            }
            $targetPath = $targetDir.$relativePathname;
            if ($fs->exists($targetPath)) {
                $io->text(sprintf('file %s already exists', $targetPath));
            } else {
                $fs->dumpFile($targetPath, $content);
                $io->text(sprintf('Created file %s', $targetPath));
            }
        }
    }
}
