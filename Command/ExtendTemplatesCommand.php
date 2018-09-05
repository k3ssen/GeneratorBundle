<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ExtendTemplatesCommand extends Command
{
    protected static $defaultName = 'generator:extend-templates';

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

        $originDir = $this->getOriginDirectory();
        $targetDir = $this->getTargetDirectory();

        $finder = new Finder();
        $finder->files()->in($originDir);

        foreach ($finder as $file) {
            $relativePathname = $file->getRelativePathname();

            $content = $this->createContentForFile($file);
            $targetPath = $targetDir.$relativePathname;
            if ($fs->exists($targetPath)) {
                $io->text(sprintf('file %s already exists', $targetPath));
            } else {
                $fs->dumpFile($targetPath, $content);
                $io->text(sprintf('Created file %s', $targetPath));
            }
        }
    }

    protected function createContentForFile(SplFileInfo $file): string
    {
        $relativePathname = $file->getRelativePathname();

        $content = "{# @var meta_entity \K3ssen\GeneratorBundle\MetaData\MetaEntityInterface #}";
        // With the exception of the entity and repository all files should have 'generate_options'
        if (stripos($relativePathname, 'entity') === false && stripos($relativePathname, 'repository') === false) {
            $content .= "\n{# @var generate_options \K3ssen\GeneratorBundle\Generator\CrudGenerateOptions#}";
        }

        if ($file->getFilename()[0] === '_') {
            $content .= "\n{% use '@!Generator/".$relativePathname."' %}";
        } else {
            $content .= "\n{% extends '@!Generator/".$relativePathname."' %}";
        }
        return $content;
    }

    protected function getOriginDirectory(): string
    {
        return __DIR__ . '/../Resources/views';
    }

    protected function getTargetDirectory(): string
    {
        return $this->projectDir  . '/templates/bundles/GeneratorBundle/';
    }
}
