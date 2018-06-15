<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Generator;

use Doctrine\Common\Util\Inflector;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\Reader\BundleProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Config\FileLocator;

class CrudGenerator
{
    use GeneratorFileLocatorTrait;

    /** @var \Twig_Environment */
    protected $twig;
    /** @var string */
    protected $projectDir;
    /** @var CrudGenerateOptions */
    protected $generateOptions = null;
    /** @var BundleProvider */
    protected $bundleProvider;

    public function __construct(
        FileLocator $fileLocator,
        \Twig_Environment $twig,
        string $projectDir,
        BundleProvider $bundleProvider,
        CrudGenerateOptions $generateOptions
    ) {
        $this->fileLocator = $fileLocator;
        $this->twig = $twig;
        $this->projectDir = $projectDir;
        $this->bundleProvider = $bundleProvider;
        $this->generateOptions = $generateOptions;
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
        $this->setTwigEscapeStrategy();
        $this->generateOptions->setDefaultBundleNamespace($this->bundleProvider->getDefaultBundleNameSpace());

        $files[] = $this->createFile($metaEntity,'Controller', 'Controller', $this->generateOptions->getControllerSubdirectory());
        $files[] = $this->createFile($metaEntity,'Form', 'Type');;
        if ($this->generateOptions->getUseVoter()) {
            $files[] = $this->createFile($metaEntity,'Security', 'Voter');
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

    protected function setTwigEscapeStrategy()
    {
        /** @var \Twig_Extension_Escaper $escaper */
        $escaper = $this->twig->getExtension(\Twig_Extension_Escaper::class);
        // Set escape strategy to false, so that we don't need '{% autoescape false %} for many statements.
        // We're generating raw code after all, so escaping is rarely needed in this case.
        $escaper->setDefaultStrategy(false);
    }

    protected function createFile(MetaEntityInterface $metaEntity, string $dirName, string $fileSuffixName, string $subDirName = null): ?string
    {
        $targetDir = '/'.$dirName. ($subDirName ? '/'.Inflector::classify($subDirName) : '');
        $targetFile = str_replace(['/Entity', '.php'], [$targetDir, $fileSuffixName.'.php'], $this->getTargetFile($metaEntity));
        $fileContent = $this->render(strtolower($dirName).'/'.$fileSuffixName.'.php.twig', $metaEntity);
        $this->getFileSystem()->dumpFile($targetFile, $fileContent);

        return $targetFile;
    }

    protected function createViewTemplate(MetaEntityInterface $metaEntity, $action): string
    {
        $fileContent = $this->render('templates/'.$action.'.html.twig.twig', $metaEntity);

        $targetSubdir = $this->generateOptions->getControllerSubdirectory();
        $targetFile = $this->projectDir .'/templates/'.
            ($targetSubdir ? Inflector::tableize($targetSubdir).'/' : '').
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