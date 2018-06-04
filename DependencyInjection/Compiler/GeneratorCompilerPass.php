<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\DependencyInjection\Compiler;

use K3ssen\GeneratorBundle\Command\AttributeQuestion\AttributeQuestionInterface;
use K3ssen\GeneratorBundle\Command\AttributeQuestion\BasicAttributeQuestion;
use K3ssen\GeneratorBundle\Command\EntityQuestion\EntityQuestionInterface;
use K3ssen\GeneratorBundle\Command\PropertyQuestion\PropertyQuestionInterface;
use K3ssen\GeneratorBundle\MetaData\ClassAnnotation\MetaAnnotationInterface;
use K3ssen\GeneratorBundle\MetaData\Interfaces\MetaInterfaceFactory;
use K3ssen\GeneratorBundle\MetaData\Interfaces\MetaInterfaceInterface;
use K3ssen\GeneratorBundle\MetaData\MetaEntityFactory;
use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;
use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyFactory;
use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\PropertyAttribute\MetaAttributeFactory;
use K3ssen\GeneratorBundle\MetaData\PropertyAttribute\MetaAttributeInterface;
use K3ssen\GeneratorBundle\MetaData\ClassAnnotation\MetaAnnotationFactory;
use K3ssen\GeneratorBundle\MetaData\Traits\MetaTraitFactory;
use K3ssen\GeneratorBundle\MetaData\Traits\MetaTraitInterface;
use K3ssen\GeneratorBundle\MetaData\Validation\MetaValidationFactory;
use K3ssen\GeneratorBundle\MetaData\Validation\MetaValidationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GeneratorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $definition) {
            if ($definition->getClass() && class_exists($definition->getClass(), false)) {
                if (is_subclass_of($definition->getClass(), MetaPropertyInterface::class, true)) {
                    $container->getDefinition(MetaPropertyFactory::class)->addMethodCall('addMetaPropertyClass', [$definition->getClass()]);
                    continue;
                }
                if (is_subclass_of($definition->getClass(), MetaEntityInterface::class, true)) {
                    $container->getDefinition(MetaEntityFactory::class)->addMethodCall('setMetaEntityClass', [$definition->getClass()]);
                    continue;
                }
                if (is_subclass_of($definition->getClass(), MetaAttributeInterface::class, true)) {
                    $container->getDefinition(MetaAttributeFactory::class)->addMethodCall('setMetaAttributeClass', [$definition->getClass()]);
                    continue;
                }
                if (is_subclass_of($definition->getClass(), MetaInterfaceInterface::class, true)) {
                    $container->getDefinition(MetaInterfaceFactory::class)->addMethodCall('setMetaInterfaceClass', [$definition->getClass()]);
                    continue;
                }
                if (is_subclass_of($definition->getClass(), MetaValidationInterface::class, true)) {
                    $container->getDefinition(MetaValidationFactory::class)->addMethodCall('setMetaValidationClass', [$definition->getClass()]);
                    continue;
                }
                if (is_subclass_of($definition->getClass(), MetaTraitInterface::class, true)) {
                    $container->getDefinition(MetaTraitFactory::class)->addMethodCall('setMetaTraitClass', [$definition->getClass()]);
                    continue;
                }
                if (is_subclass_of($definition->getClass(), MetaAnnotationInterface::class, true)) {
                    $container->getDefinition(MetaAnnotationFactory::class)->addMethodCall('setMetaAnnotationClass', [$definition->getClass()]);
                    continue;
                }

                if (is_subclass_of($definition->getClass(), EntityQuestionInterface::class, true)) {
                    $attributes = [];
                    if (defined($definition->getClass().'::PRIORITY')) {
                        $attributes['priority'] = constant($definition->getClass().'::PRIORITY');
                    }
                    $definition->addTag('generator.entity_question', $attributes);
                    continue;
                }
                if (is_subclass_of($definition->getClass(), PropertyQuestionInterface::class, true)) {
                    $priority = 0;
                    if (defined($definition->getClass().'::PRIORITY')) {
                        $priority = constant($definition->getClass().'::PRIORITY');
                    }
                    $definition->addTag('generator.property_question', ['priority' => $priority]);
                    continue;
                }
            }
        }

        foreach ($container->getParameter('generator.attributes') as $attributeName => $attributeInfo) {
            if(array_key_exists('question', $attributeInfo)) {
                $serviceId = $attributeInfo['question'] ?: null;
                if (!$serviceId) {
                    continue;
                }
            } else {
                $serviceId = BasicAttributeQuestion::class;
            }
            $definition = $container->getDefinition($serviceId);
            if (!is_subclass_of($definition->getClass(), AttributeQuestionInterface::class,true)) {
                throw new InvalidDefinitionException(sprintf('Question service for attribute must implement "%s"; got "%s"', AttributeQuestionInterface::class, $definition->getClass()));
            }
            $definition->addMethodCall('addAttribute', [$attributeName, $attributeInfo]);
            if ($definition->hasTag('generator.attribute_question') === false) {
                $definition->addTag('generator.attribute_question');
            }
        }
    }
}