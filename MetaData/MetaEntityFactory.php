<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData;

use Doctrine\ORM\EntityManagerInterface;
use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyFactory;
use K3ssen\GeneratorBundle\MetaData\Property\RelationMetaPropertyInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use K3ssen\GeneratorBundle\MetaData\ClassAnnotation\MetaAnnotationFactory;
use K3ssen\GeneratorBundle\Reader\BundleProvider;

class MetaEntityFactory
{
    /** @var string */
    protected $metaEntityClass;

    /** @var BundleProvider */
    protected $bundleProvider;

    /** @var MetaPropertyFactory */
    protected $metaPropertyFactory;

    /** @var bool */
    protected $useCustomRepository;

    /** @var ClassMetadataFactory */
    protected $classMetadataFactory;

    /** @var MetaAnnotationFactory */
    protected $metaAnnotationFactory;

    public function __construct(
        BundleProvider $bundleProvider,
        ?bool $useCustomRepository,
        MetaPropertyFactory $metaPropertyFactory,
        MetaAnnotationFactory $metaAnnotationFactory,
        EntityManagerInterface $em
    )
    {
        $this->bundleProvider = $bundleProvider;
        $this->useCustomRepository = $useCustomRepository;
        $this->metaPropertyFactory = $metaPropertyFactory;
        $this->metaAnnotationFactory = $metaAnnotationFactory;
        $this->classMetadataFactory = $em->getMetadataFactory();
    }

    public function setMetaEntityClass(string $className)
    {
        $this->metaEntityClass = $className;
    }

    /**
     * Creates a MetaEntity by the provided ClassName.
     * Preferably the fullClassName is provided, so that bundle and subdirectory can be automatically subtracted.
     * If only a name (without namespace) is provided, defaults will be used.
     */
    public function createByClassName(string $nameOrFullClassName): MetaEntityInterface
    {
        /** @var MetaEntityInterface $metaEntity */
        $metaEntity = new $this->metaEntityClass($nameOrFullClassName);
        $metaEntity->setUseCustomRepository($this->useCustomRepository);
        $ormTableAnnotation = $this->metaAnnotationFactory->createMetaAnnotation(
            $metaEntity,
            ['Doctrine\ORM\Mapping' => 'ORM'],
            'ORM\Table',
            ['name' => $metaEntity->getTableName()]
        ) ;
        $ormEntityAnnotation = $this->metaAnnotationFactory->createMetaAnnotation(
            $metaEntity,
            ['Doctrine\ORM\Mapping' => 'ORM'],
            'ORM\Entity'
        ) ;
        if ($metaEntity->hasCustomRepository()) {
            $ormEntityAnnotation->addAnnotationAttribute('repositoryClass', $metaEntity->getRepositoryFullClassName());
        }
        return $metaEntity
            ->addEntityAnnotation($ormTableAnnotation)
            ->addEntityAnnotation($ormEntityAnnotation)
        ;
    }

    /**
     * Creates a MetaEntity by shortcutNotation rather than (full)ClassName.
     * Unlike with the fullClassName, you'd only need to provide the bundleName rather than its namespace.
     *
     * Possible notations are:
     *  - BundleName:Subdirectory/MetaEntityName    to specify bundle, subdir and entityName
     *  - BundleName:MetaEntityName                 to specify bundle and entityName, but no subdir
     *  - SubDirectory/EntityName                   to specify subDir and entityName, but no bundle
     *  - EntityName                                to specify entityName, but no subDir and no bundle
     */
    public function createByShortcutNotation(string $shortcutNotation): MetaEntityInterface
    {
        $entityName = $shortcutNotation;
        $bundleName = $subDir = null;
        if (strpos($shortcutNotation, ':') !== false) {
            $parts = explode(':', $shortcutNotation);
            $bundleName = array_shift($parts);
            $entityName =  implode('/', $parts);
        }
        if (strpos($shortcutNotation, '/') !== false) {
            $parts = explode('/', $shortcutNotation);
            $entityName = array_pop($parts);
            $subDir = implode('/', $parts);
        }
        $bundleNamespace = $this->bundleProvider->getBundleNamespaceByName($bundleName);
        $fullClassName = $bundleNamespace . '\\Entity\\' . ($subDir ? $subDir.'\\' : '') . $entityName;
        return $this->createByClassName($fullClassName);
    }

    /**
     * Retrieves list of existing entities as MetaEntities (only if fullClassName is set on these MetaEntities)
     * @return array|MetaEntityInterface[]
     */
    public function getEntityOptions(): array
    {
        if (isset($this->existingEntities)) {
            return $this->existingEntities;
        }
        /** @var ClassMetadata[] $entityMetadata */
        $entityMetadata = $this->classMetadataFactory->getAllMetadata();

        $entities = [];
        foreach ($entityMetadata as $meta) {
            $entities[] = $this->createByClassName($meta->getName());
        }
        return $this->existingEntities = $entities;
    }

    /**
     * Retrieves list of existing entities as strings (only if fullClassName is set on the corresponding MetaEntities)
     * @return array|string[]
     */
    public function getEntityOptionsAsStrings(): array
    {
        $options = $this->getEntityOptions();
        foreach ($options as $key => $option) {
            $options[$key] = (string) $option;
        }
        return $options;
    }

    public function getMetaEntityByChosenOption($choice): ?MetaEntityInterface
    {
        $options = $this->getEntityOptions();
        foreach ($options as $key => $metaEntity) {
            if ((string) $metaEntity === $choice) {
                return $metaEntity;
            }
        }
        return null;
    }

    public function getDoctrineOrmClassMetadata(string $entityFullClassName): ClassMetadata
    {
        $classMetaData = $this->classMetadataFactory->getMetadataFor($entityFullClassName);
        return $classMetaData instanceof ClassMetadata ? $classMetaData : null;
    }

    /**
     * Adds missing field for inversedBy or mappedBy
     */
    public function addMissingProperty(MetaEntityInterface $metaEntity, RelationMetaPropertyInterface $property)
    {
        $inversedBy = $property->getInversedBy();
        $mappedBy = $property->getMappedBy();
        if ($newPropertyName = $mappedBy ?: $inversedBy) {
            $inversedType = MetaPropertyFactory::getInversedType($property->getOrmType());
            /** @var RelationMetaPropertyInterface $newProperty */
            $newProperty = $this->metaPropertyFactory->createMetaProperty(
                $metaEntity,
                $inversedType,
                $newPropertyName
            );
            $newProperty->setTargetEntity($property->getMetaEntity());
            if ($inversedBy) {
                $newProperty->setMappedBy($property->getName());
            } else {
                $newProperty->setInversedBy($property->getName());
            }
        }
    }
}