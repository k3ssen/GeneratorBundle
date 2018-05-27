<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\EntityQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\MetaData\ClassAnnotation\MetaAnnotationFactory;
use K3ssen\GeneratorBundle\MetaData\Traits\MetaTraitFactory;

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
     * @var bool
     */
    protected $askTraits;

    protected $traitOptions = [];

    public function __construct(MetaTraitFactory $metaTraitFactory, MetaAnnotationFactory $metaAnnotationFactory, bool $askTraits, array $traitOptions)
    {
        $this->askTraits = $askTraits;
        $this->metaTraitFactory = $metaTraitFactory;
        $this->metaAnnotationFactory = $metaAnnotationFactory;
        $this->traitOptions = $traitOptions;
    }

    public function addActions(CommandInfo $commandInfo, array &$actions)
    {
        if ($this->askTraits) {
            $actions['Set traits'] = function () use ($commandInfo) {
                $this->doQuestion($commandInfo);
            };
        }
    }

    public function doQuestion(CommandInfo $commandInfo)
    {
        if (!$this->askTraits) {
            return;
        }
        $metaEntity =  $commandInfo->getMetaEntity();
        $commandInfo->getIo()->text('What traits should be used?');
        foreach ($this->traitOptions as $description => $namespace) {
            if (!$namespace) {
                continue;
            }
            if (!trait_exists($namespace)) {
                $commandInfo->getIo()->text(sprintf('Cannot ask trait "%s"; trait not found', $namespace));
                continue;
            }
            if ($commandInfo->getIo()->confirm($description)) {
                $metaTrait = $this->metaTraitFactory->createMetaTrait($metaEntity, $namespace);
                $metaEntity->addTrait($metaTrait);
                $this->addSpecialOptions($metaEntity, $description, $namespace);
            } else {
                $this->removeTraitIfExists($metaEntity, $namespace);
                $this->undoSpecialOptions($metaEntity, $description, $namespace);
            }
        }
    }

    protected function addSpecialOptions(MetaEntityInterface $metaEntity, $description, $namespace)
    {
        if (stripos ($description.$namespace, 'softdelete') !== false) {
            $this->addSoftDeleteAnnotation($metaEntity);
        }
    }

    protected function undoSpecialOptions(MetaEntityInterface $metaEntity, $description, $namespace)
    {
        if (stripos ($description.$namespace, 'softdelete') !== false) {
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

    protected function removeTraitIfExists(MetaEntityInterface $metaEntity, string $namespace)
    {
        $metaTrait = $metaEntity->getTraits()[$namespace] ?? null;
        if ($metaTrait) {
            $metaEntity->removeTrait($metaTrait);
        }
    }
}