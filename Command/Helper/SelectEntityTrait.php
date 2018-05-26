<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\Helper;

use K3ssen\GeneratorBundle\MetaData\MetaEntityFactory;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

trait SelectEntityTrait
{
    /** @var MetaEntityFactory */
    protected $metaEntityFactory;

    protected function selectEntity(SymfonyStyle $io): ?MetaEntityInterface
    {
        $choices = $this->metaEntityFactory->getEntityOptions();
        if (count($choices) === 0) {
            $io->error('No entities found; Add some entities first.');
            return null;
        }
        $choice = $io->choice('Entity', $choices);
        return $this->metaEntityFactory->getMetaEntityByChosenOption($choice);
    }
}
