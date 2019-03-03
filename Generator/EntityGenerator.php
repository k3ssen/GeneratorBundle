<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Generator;

use K3ssen\GeneratorBundle\MetaData\MetaEntityFactory;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\MetaData\Property\RelationMetaPropertyInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Config\FileLocator;

class EntityGenerator
{
    use GeneratorFileLocatorTrait;

    /** @var MetaEntityFactory */
    protected $metaEntityFactory;

    /** @var EntityAppender */
    protected $entityAppender;

    /** @var \Twig_Environment */
    protected $twig;

    public function __construct(
        MetaEntityFactory $metaEntityFactory,
        EntityAppender $entityAppender,
        FileLocator $fileLocator,
        \Twig_Environment $twig
    ) {
        $this->entityAppender = $entityAppender;
        $this->metaEntityFactory = $metaEntityFactory;
        $this->fileLocator = $fileLocator;
        $this->twig = $twig;
    }

    public function createEntity(MetaEntityInterface $metaEntity): array
    {
        $entityFileData = $this->getEntityContent($metaEntity);

        $targetFile = $this->getTargetFile($metaEntity);

        $fs = new Filesystem();
        $fs->dumpFile($targetFile, $entityFileData);
        $affectedFiles[] = $targetFile;

        if ($metaEntity->hasCustomRepository()) {
            $affectedFiles[] = $this->createRepository($metaEntity);
        }

        return array_merge($affectedFiles, $this->generateMissingInversedOrMappedBy($metaEntity));
    }

    public function updateEntity(MetaEntityInterface $pseudoMetaEntity): array
    {
        return array_merge(
            [$this->entityAppender->appendFields($pseudoMetaEntity)],
            $this->generateMissingInversedOrMappedBy($pseudoMetaEntity)
        );
    }

    protected function generateMissingInversedOrMappedBy(MetaEntityInterface $metaEntity): array
    {
        $affectedFiles = [];
        foreach ($metaEntity->getRelationshipProperties() as $property) {
            $targetMetaEntity = $property->getTargetEntity();
            $fullClassName = $targetMetaEntity ? $targetMetaEntity->getFullClassName() : null;
            $existingClass = $fullClassName ? class_exists($fullClassName) : false;
            if (!$targetMetaEntity || ($existingClass && $this->checkEntityHasProperty($fullClassName, $property))) {
                continue;
            }
            $this->metaEntityFactory->addMissingProperty($targetMetaEntity, $property);
            if ($existingClass) {
                $affectedFiles[] = $this->entityAppender->appendFields($targetMetaEntity);
            } else {
                $affectedFiles = array_merge($affectedFiles, $this->createEntity($targetMetaEntity));
            }
        }
        return $affectedFiles;
    }

    protected function checkEntityHasProperty($fullClassName, RelationMetaPropertyInterface $property): bool
    {
        foreach ((new \ReflectionClass($fullClassName))->getProperties() as $reflectionProperty) {
            if (\in_array($reflectionProperty->getName(), [$property->getMappedBy(), $property->getInversedBy()])) {
                return true;
            }
        }
        return false;
    }

    public function createRepository(MetaEntityInterface $metaEntity): string
    {
        $repoFileData = $this->getRepositoryContent($metaEntity);
        $targetFile = str_replace([DIRECTORY_SEPARATOR.'Entity', '.php'], [DIRECTORY_SEPARATOR.'Repository', 'Repository.php'], $this->getTargetFile($metaEntity));

        $fs = new Filesystem();
        $fs->dumpFile($targetFile, $repoFileData);

        return $targetFile;
    }

    protected function getRepositoryContent(MetaEntityInterface $metaEntity)
    {
        return $this->twig->render('@Generator/skeleton/repository/Repository.php.twig', [
            'meta_entity' => $metaEntity,
        ]);
    }

    protected function getEntityContent(MetaEntityInterface $metaEntity)
    {
        return $this->twig->render('@Generator/skeleton/entity/Entity.php.twig', [
            'meta_entity' => $metaEntity,
        ]);
    }
}