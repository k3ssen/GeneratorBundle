<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Generator;

use Doctrine\Common\Inflector\Inflector;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\Reader\BundleProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Config\FileLocator;

class CrudGenerator
{
    use GeneratorFileLocatorTrait;

    /** @var \Twig\Environment */
    protected $twig;
    /** @var string */
    protected $projectDir;
    /** @var CrudGenerateOptions */
    protected $generateOptions = null;
    /** @var BundleProvider */
    protected $bundleProvider;

    public function __construct(
        FileLocator $fileLocator,
        \Twig\Environment $twig,
        string $projectDir,
        BundleProvider $bundleProvider,
        CrudGenerateOptions $generateOptions
    ) {
        $this->fileLocator = $fileLocator;
        $this->twig = $twig;
        $this->projectDir = $projectDir;
        $this->bundleProvider = $bundleProvider;
        $this->generateOptions = $generateOptions;
        $this->generateOptions->setDefaultBundleNamespace($this->bundleProvider->getDefaultBundleNameSpace());
    }

    protected function getFileSystem(): Filesystem
    {
        if (!isset($this->fileSystem)) {
            $this->fileSystem = new Filesystem();
        }
        return $this->fileSystem;
    }

    public function createCrud(MetaEntityInterface $metaEntity): array
    {
        $files = $this->createController($metaEntity);

        $files = array_merge($files, $this->createForm($metaEntity));
        if ($this->generateOptions->getUseVoter()) {
            $files = array_merge($files, $this->createVoter($metaEntity));
        }
        if ($this->generateOptions->getUseDatatable()) {
            $files = array_merge($files, $this->createDatatable($metaEntity));
        }
        $files[] = $this->createViewTemplate($metaEntity, 'index');
        $files[] = $this->createViewTemplate($metaEntity, 'show');
        if ($this->generateOptions->getUseWriteActions()) {
            $files[] = $this->createViewTemplate($metaEntity, 'new');
            $files[] = $this->createViewTemplate($metaEntity, 'edit');
            $files[] = $this->createViewTemplate($metaEntity, 'delete');
        }
        return array_filter($files);
    }

    public function createController(MetaEntityInterface $metaEntity): array
    {
        return [
            $this->createBaseClassIfMissing('Controller', 'AbstractController'),
            $this->createFile($metaEntity,'Controller', 'Controller', $this->generateOptions->getControllerSubdirectory()),
        ];
    }

    public function createForm(MetaEntityInterface $metaEntity): array
    {
        return [$this->createFile($metaEntity,'Form', 'Type')];
    }

    public function createVoter(MetaEntityInterface $metaEntity): array
    {
        return [
            $this->createBaseClassIfMissing('Security', 'AbstractVoter'),
            $this->createFile($metaEntity,'Security', 'Voter'),
        ];
    }

    public function createDatatable(MetaEntityInterface $metaEntity): array
    {
        return [
            $this->createBaseClassIfMissing('Datatable', 'AbstractDatatable'),
            $this->createFile($metaEntity,'Datatable', 'Datatable'),
        ];
    }

    public function createViewTemplate(MetaEntityInterface $metaEntity, $action): string
    {
        $fileContent = $this->render('templates/'.$action.'.txt.twig', $metaEntity);

        $templatesTargetDir = rtrim($this->generateOptions->getTemplatesDirectory() ?: $this->projectDir .'/templates', '/') . '/';

        $targetSubdir = $this->generateOptions->getControllerSubdirectory();
        $targetFile = $templatesTargetDir.
            ($targetSubdir ? Inflector::tableize($targetSubdir).'/' : '').
            Inflector::tableize($metaEntity->getName()). '/'.
            $this->getViewTemplateFileName($action);
        ;
        $this->getFileSystem()->dumpFile($targetFile, $fileContent);

        return $targetFile;
    }

    protected function getViewTemplateFileName($action): string
    {
        return $action . '.' . trim($this->generateOptions->getTemplatesFileExtension(), '.');
    }

    protected function createFile(MetaEntityInterface $metaEntity, string $dirName, string $fileSuffixName, string $subDirName = null): ?string
    {
        $targetDir = '/'.$dirName. ($subDirName ? '/'.Inflector::classify($subDirName) : '');
        $targetFile = str_replace([DIRECTORY_SEPARATOR.'Entity', '.php'], [$targetDir, $fileSuffixName.'.php'], $this->getTargetFile($metaEntity));
        $fileContent = $this->render(strtolower($dirName).'/'.$fileSuffixName.'.php.twig', $metaEntity);
        $this->getFileSystem()->dumpFile($targetFile, $fileContent);

        return $targetFile;
    }

    protected function createBaseClassIfMissing(string $dirName, string $fileSuffixName): ?string
    {
        $defaultBundlePath = $this->bundleProvider->getDefaultBundlePath();
        $targetFile = $defaultBundlePath.'/'.$dirName.'/'.$fileSuffixName.'.php';
        if ($this->getFileSystem()->exists($targetFile)) {
            return null;
        }
        $fileContent = $this->twig->render('@Generator/skeleton/'.strtolower($dirName).'/'.$fileSuffixName.'.php.twig', [
            'generate_options' => $this->generateOptions,
        ]);
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