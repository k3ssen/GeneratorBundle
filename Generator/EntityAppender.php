<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Generator;

use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\Reader\ExistingEntityToMetaEntityReader;
use Symfony\Component\HttpKernel\Config\FileLocator;

class EntityAppender
{
    use GeneratorFileLocatorTrait;

    /** @var \Twig_Environment */
    protected $twig;
    /**
     * @var ExistingEntityToMetaEntityReader
     */
    protected $existingEntityToMetaEntityReader;

    public function __construct(
        ExistingEntityToMetaEntityReader $existingEntityToMetaEntityReader,
        FileLocator $fileLocator,
        \Twig_Environment $twig
    ) {
        $this->existingEntityToMetaEntityReader = $existingEntityToMetaEntityReader;
        $this->fileLocator = $fileLocator;
        $this->twig = $twig;
    }

    public function appendFields(MetaEntityInterface $pseudoMetaEntity): string
    {
        $diffMetaEntity = $this->getMetaEntityDiff($pseudoMetaEntity);
        $targetFile = $this->getTargetFile($diffMetaEntity);
        $currentContent = file_get_contents($targetFile);

        $this->addUsages($diffMetaEntity, $currentContent);
        $this->addConstructorContent($diffMetaEntity, $currentContent);
        $this->addProperties($diffMetaEntity, $currentContent);
        $this->getAddedMethods($diffMetaEntity, $currentContent);

        file_put_contents($targetFile, $currentContent);
        return $targetFile;
    }

    protected function getMetaEntityDiff(MetaEntityInterface $pseudoMetaEntity): MetaEntityInterface
    {
        $currentMetaEntity = $this->existingEntityToMetaEntityReader->createMetaEntityFromClass($pseudoMetaEntity->getFullClassName());
        $diffMetaEntity = clone $pseudoMetaEntity;
        foreach ($currentMetaEntity->getUsages() as $usage) {
            $diffMetaEntity->removeUsage($usage);
        }
        foreach ($currentMetaEntity->getProperties() as $property) {
            foreach ($diffMetaEntity->getProperties() as $diffProperty) {
                if ($diffProperty->getName() === $property->getName()) {
                    $diffMetaEntity->removeProperty($diffProperty);
                }
            }
        }
        return $diffMetaEntity;
    }

    protected function addUsages(MetaEntityInterface $diffMetaEntity, string &$currentContent)
    {
        //First we check and remove usages that are already defined.
        foreach ($diffMetaEntity->getUsages() as $usageNamespace => $usageAlias) {
            if (strpos($currentContent, $usageNamespace) !== false) {
                $diffMetaEntity->removeUsage($usageNamespace);
            }
        }
        $usageContent = $this->twig->render('@Generator/skeleton/entity/_usages.php.twig', [
            'meta_entity' => $diffMetaEntity,
        ]);

        $this->insertStrAfterLastMatch($currentContent, $usageContent, '/use (\w+\\\\.+);/');
    }

    protected function addConstructorContent(MetaEntityInterface $diffMetaEntity, string &$currentContent)
    {
        $hasConstructor = strpos($currentContent, 'public function __construct(') !== false;
        $propertyContent = $this->twig->render('@Generator/skeleton/entity/_construct.php.twig', [
            'meta_entity' => $diffMetaEntity,
            'inner_content_only' => $hasConstructor,
        ]);
        if ($hasConstructor) {
            $this->insertStrAfterLastMatch($currentContent, $propertyContent, '/public function __construct\(.*\)\n    /');
        } else {
            $this->insertStrAfterLastMatch($currentContent, $propertyContent, '/(protected|private|public) \$\w+;/');
        }
    }

    protected function addProperties(MetaEntityInterface $diffMetaEntity, string &$currentContent)
    {
        $propertyContent = $this->twig->render('@Generator/skeleton/entity/properties.php.twig', [
            'meta_entity' => $diffMetaEntity,
            'skip_id' => true,
        ]);
        $this->insertStrAfterLastMatch($currentContent, $propertyContent, '/(protected|private|public) \$\w+;/');
    }

    protected function getAddedMethods(MetaEntityInterface $diffMetaEntity, string &$currentContent)
    {
        $methodsContent = $this->twig->render('@Generator/skeleton/entity/property_methods.php.twig', [
            'meta_entity' => $diffMetaEntity,
            'skip_id' => true,
        ]);

        preg_match_all('/\}/', $currentContent, $matches, PREG_OFFSET_CAPTURE);
        $lastMatch = array_pop($matches[0]);
        $position = $lastMatch[1];
        $currentContent = substr_replace($currentContent, $methodsContent, $position, 0);
    }

    protected function insertStrAfterLastMatch(string &$baseString, string $insertString, string $pattern)
    {
        preg_match_all($pattern, $baseString, $matches, PREG_OFFSET_CAPTURE);
        $lastMatch = array_pop($matches[0]);
        if (is_array($lastMatch) && count($lastMatch) > 1) {
            $position = $lastMatch[1] + strlen($lastMatch[0]) + 1;
            $baseString = substr_replace($baseString, $insertString, $position, 0);
        }
    }
}
