<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\AttributeQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\MetaData\Interfaces\MetaInterfaceFactory;
use K3ssen\GeneratorBundle\MetaData\PropertyAttribute\MetaAttributeInterface;

class IdAttributeQuestion extends BasicAttributeQuestion
{
    /**
     * @var MetaInterfaceFactory
     */
    private $metaInterfaceFactory;

    public function __construct(MetaInterfaceFactory $metaInterfaceFactory)
    {
        $this->metaInterfaceFactory = $metaInterfaceFactory;
    }

    public function doQuestion(CommandInfo $commandInfo, MetaAttributeInterface $metaAttribute)
    {
        parent::doQuestion($commandInfo, $metaAttribute);

        if ($metaAttribute->getValue() === true) {
            $metaEntity = $commandInfo->getMetaEntity();
            $identifiableInterface = 'K3ssen\BaseAdminBundle\Model\IdentifiableInterface';
            $metaInterface = $this->metaInterfaceFactory->createMetaInterface($metaEntity, $identifiableInterface);
            $metaEntity->addInterface($metaInterface);
        }
    }
}