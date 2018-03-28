<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Generator;

use Doctrine\Common\Util\Inflector;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Config\FileLocator;

class CrudGenerator
{
    use GeneratorFileLocatorTrait;

    /** @var \Twig_Environment */
    protected $twig;

    protected $projectDir;

    protected $generateOptions = null;

    public function __construct(FileLocator $fileLocator, \Twig_Environment $twig, string $projectDir) {
        $this->fileLocator = $fileLocator;
        $this->twig = $twig;
        $this->projectDir = $projectDir;
    }

    protected function getFileSystem(): Filesystem
    {
        if (!isset($this->fileSystem)) {
            $this->fileSystem = new Filesystem();
        }
        return $this->fileSystem;
    }

    public function createCrud(MetaEntityInterface $metaEntity, CrudGenerateOptions $generateOptions = null): array
    {
        $this->generateOptions = $generateOptions ?: new CrudGenerateOptions();
        $files[] = $this->createController($metaEntity);
        $files[] = $this->createForm($metaEntity);
        if ($this->generateOptions->isUsingVoters()) {
            $files[] = $this->createVoter($metaEntity);
        }
        if ($this->generateOptions->isUsingDatatable()) {
            $files[] = $this->createDatatable($metaEntity);
        }
        $files[] = $this->createViewTemplate($metaEntity, 'index');
        $files[] = $this->createViewTemplate($metaEntity, 'show');
        if ($this->generateOptions->isUsingWriteActions()) {
            $files[] = $this->createViewTemplate($metaEntity, 'new');
            $files[] = $this->createViewTemplate($metaEntity, 'edit');
            $files[] = $this->createViewTemplate($metaEntity, 'delete');
        }
        return $files;
    }

    protected function createVoter(MetaEntityInterface $metaEntity): string
    {
        return $this->createFile($metaEntity,'Security', 'Voter');
    }

    protected function createDatatable(MetaEntityInterface $metaEntity): string
    {
        return $this->createFile($metaEntity,'Datatable', 'Datatable');
    }

    protected function createController(MetaEntityInterface $metaEntity): string
    {
        return $this->createFile($metaEntity,'Controller', 'Controller');
    }

    protected function createForm(MetaEntityInterface $metaEntity): string
    {
        return $this->createFile($metaEntity,'Form', 'Type');
    }

    protected function createFile(MetaEntityInterface $metaEntity, $dirName, $fileSuffixName): string
    {
        $fileContent = $this->render(strtolower($dirName).'/'.strtolower($fileSuffixName).'.php.twig', $metaEntity);

        $targetFile = str_replace(['/Entity', '.php'], ['/'.$dirName, $fileSuffixName.'.php'], $this->getTargetFile($metaEntity));

        $this->getFileSystem()->dumpFile($targetFile, $fileContent);

        return $targetFile;
    }

    protected function createViewTemplate(MetaEntityInterface $metaEntity, $action): string
    {
        $fileContent = $this->render('templates/'.$action.'.html.twig.twig', $metaEntity);

        $targetFile = $this->projectDir .'/templates/'.
            ($metaEntity->getSubDir() ? Inflector::tableize($metaEntity->getSubDir()).'/' : '').
            Inflector::tableize($metaEntity->getName()). '/'.
            $action.'.html.twig';
        ;
        $this->getFileSystem()->dumpFile($targetFile, $fileContent);

        return $targetFile;
    }

    protected function render($skeletonTwigFile, MetaEntityInterface $metaEntity, array $params = [])
    {
        return $this->twig->render('@Generator/skeleton/'.$skeletonTwigFile, array_merge([
            'meta_entity' => $metaEntity,
            'generate_options' => $this->generateOptions,
        ], $params));
    }
}