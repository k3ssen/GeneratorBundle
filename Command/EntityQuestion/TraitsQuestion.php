<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\EntityQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\Generator\EntityGenerateOptions;
use K3ssen\GeneratorBundle\MetaData\Interfaces\MetaInterfaceFactory;
use K3ssen\GeneratorBundle\MetaData\Interfaces\MetaInterfaceInterface;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\MetaData\ClassAnnotation\MetaAnnotationFactory;
use K3ssen\GeneratorBundle\MetaData\Traits\MetaTraitFactory;
use K3ssen\GeneratorBundle\MetaData\Traits\MetaTraitInterface;

class TraitsQuestion implements EntityQuestionInterface
{
    public const PRIORITY = 65;

    /**
     * @var MetaTraitFactory
     */
    protected $metaTraitFactory;
    /**
     * @var MetaAnnotationFactory
     */
    protected $metaAnnotationFactory;
    /**
     * @var EntityGenerateOptions
     */
    protected $entityGenerateOptions;
    /**
     * @var MetaInterfaceFactory
     */
    protected $metaInterfaceFactory;

    public function __construct(
        MetaTraitFactory $metaTraitFactory,
        MetaAnnotationFactory $metaAnnotationFactory,
        EntityGenerateOptions $entityGenerateOptions,
        MetaInterfaceFactory $metaInterfaceFactory
    )
    {
        $this->metaTraitFactory = $metaTraitFactory;
        $this->metaAnnotationFactory = $metaAnnotationFactory;
        $this->entityGenerateOptions = $entityGenerateOptions;
        $this->metaInterfaceFactory = $metaInterfaceFactory;
    }

    public function addActions(CommandInfo $commandInfo, array &$actions)
    {
        if ($this->entityGenerateOptions->getAskTraits()) {
            $actions['Set traits'] = function () use ($commandInfo) {
                $this->doQuestion($commandInfo);
            };
        }
    }

    public function doQuestion(CommandInfo $commandInfo)
    {
        if (!$this->entityGenerateOptions->getAskTraits()) {
            return;
        }

        $metaEntity =  $commandInfo->getMetaEntity();
        $commandInfo->getIo()->text('What traits should be used?');
        foreach ($this->entityGenerateOptions->getTraitOptions() as $traitKey => $options) {
            if (($options['default'] === false && $options['ask'] === false) || !$metaTrait = $this->createMetaTrait($commandInfo, $traitKey, $options)) {
                continue;
            }
            $metaInterface = $this->createMetaInterfaceIfDefined($commandInfo, $traitKey, $options);

            if ($options['ask'] === false || $commandInfo->getIo()->confirm($traitKey, $options['default'])) {
                $metaEntity->addTrait($metaTrait);
                $this->addSpecialOptions($metaEntity, $traitKey, $options);
                if (isset($metaInterface)) {
                    $metaEntity->addInterface($metaInterface);
                }
            } else {
                $this->removeTraitIfExists($metaTrait);
                if (isset($metaInterface)) {
                    $this->removeInterfaceIfExists($metaInterface);
                }
                $this->undoSpecialOptions($metaEntity, $traitKey, $options);
            }
        }
    }

    protected function createMetaTrait(CommandInfo $commandInfo, string $traitKey, array $options): ?MetaTraitInterface
    {
        $namespace = $options['namespace'] ?? $traitKey;
        if (!trait_exists($namespace)) {
            $commandInfo->getIo()->text(sprintf('Cannot ask trait "%s"; namespace "%s" not found', $traitKey, $namespace));
            return null;
        }
        $metaEntity = $commandInfo->getMetaEntity();
        if ($namespaceAlias = $options['namespace_alias'] ?? null) {
            $namespace = [$namespace => $namespaceAlias];
        }
        return $this->metaTraitFactory->createMetaTrait($metaEntity, $namespace);
    }

    protected function createMetaInterfaceIfDefined(CommandInfo $commandInfo, string $traitKey, array $options): ?MetaInterfaceInterface
    {
        $metaEntity = $commandInfo->getMetaEntity();
        $interfaceNamespace = $options['interface_namespace'] ?? null;
        if (!$interfaceNamespace) {
            return null;
        }
        $interfaceAlias = $options['interface_alias'] ?? null;
        if (!interface_exists($interfaceNamespace)) {
            $commandInfo->getIo()->text(sprintf('Cannot ask trait "%s"; Interface "%s" not found', $traitKey, $interfaceNamespace));
            return null;
        }
        if ($interfaceNamespace) {
            return $this->metaInterfaceFactory->createMetaInterface($metaEntity, [$interfaceNamespace => $interfaceAlias]);
        }
        return null;
    }

    protected function addSpecialOptions(MetaEntityInterface $metaEntity, $traitKey, array $options)
    {
        if (stripos ($traitKey.$options['namespace'], 'softdelete') !== false) {
            $this->addSoftDeleteAnnotation($metaEntity);
        }
    }

    protected function undoSpecialOptions(MetaEntityInterface $metaEntity, $traitKey, array $options)
    {
        if (stripos ($traitKey.$options['namespace'], 'softdelete') !== false) {
            $this->removeSoftDeleteAnnotation($metaEntity);
        }
    }

    protected function addSoftDeleteAnnotation(MetaEntityInterface $metaEntity)
    {
        $softdeleteableAnnotation = $this->metaAnnotationFactory->createMetaAnnotation(
            $metaEntity,
            ['Gedmo\Mapping\Annotation' => 'Gedmo'],
            'Gedmo\SoftDeleteable',
            [
                'fieldName' => 'deletedAt',
                'timeAware' => false,
            ]
        );
        $metaEntity->addEntityAnnotation($softdeleteableAnnotation);
    }

    protected function removeSoftDeleteAnnotation(MetaEntityInterface $metaEntity)
    {
        foreach ($metaEntity->getEntityAnnotations() as $entityAnnotation) {
            if ($entityAnnotation->getAnnotationName() === 'Gedmo\SoftDeleteable') {
                $metaEntity->removeEntityAnnotation($entityAnnotation);
            }
        }
    }

    protected function removeTraitIfExists(MetaTraitInterface $metaTrait)
    {
        $metaEntity = $metaTrait->getMetaEntity();
        $metaTrait = $metaEntity->getTraits()[$metaTrait->getTraitUsage()] ?? null;
        if ($metaTrait) {
            $metaEntity->removeTrait($metaTrait);
        }
    }

    protected function removeInterfaceIfExists(MetaInterfaceInterface $metaInterface)
    {
        $metaEntity = $metaInterface->getMetaEntity();
        $metaInterface = $metaEntity->getInterfaces()[$metaInterface->getInterfaceUsage()] ?? null;
        if ($metaInterface) {
            $metaEntity->removeInterface($metaInterface);
        }
    }
}