<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('generator');
        $rootNode
            ->children()
                ->arrayNode('attributes')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('meta_properties')->scalarPrototype()->end()->end()
                            ->enumNode('type')
                                ->values(['string', 'int', 'bool', 'object', 'array'])
                            ->end()
                            ->scalarNode('default')->end()
                        ->end()
                        //Allow extra's for custom usage
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
                ->scalarNode('default_bundle')
                    ->defaultNull()
                ->end()
                ->booleanNode('ask_bundle')
                    ->defaultTrue()
                ->end()
                ->scalarNode('default_entity_subdirectory')
                    ->defaultNull()
                ->end()
                ->booleanNode('ask_entity_subdirectory')
                    ->defaultFalse()
                ->end()
                ->booleanNode('ask_validations')
                    ->defaultTrue()
                ->end()
                ->booleanNode('ask_display_field')
                    ->defaultTrue()
                ->end()
                ->booleanNode('ask_traits')
                    ->defaultTrue()
                ->end()
                ->arrayNode('trait_options')
                    ->arrayPrototype()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('ask')->defaultTrue()->end()
                            ->booleanNode('default')->defaultTrue()->end()
                            // Namespace should have 'isRequired', but this cases problems when just overwriting an existing trait (like Blameable) without overwriting namespace
                            ->scalarNode('namespace')->defaultNull()->end()
                            ->scalarNode('namespace_alias')->defaultNull()->end()
                            ->scalarNode('interface_namespace')->defaultNull()->end()
                            ->scalarNode('interface_alias')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('use_custom_repository')
                    ->defaultTrue()
                ->end()
                ->booleanNode('ask_use_voter')
                    ->defaultTrue()
                ->end()
                ->booleanNode('use_voter_default')
                    ->defaultTrue()
                ->end()
                ->booleanNode('ask_use_datatable')
                    ->defaultTrue()
                ->end()
                ->booleanNode('use_datatable_default')
                    ->defaultTrue()
                ->end()
                ->booleanNode('ask_use_voter')
                    ->defaultTrue()
                ->end()
                ->booleanNode('use_voter_default')
                    ->defaultTrue()
                ->end()
                ->booleanNode('ask_use_write_actions')
                    ->defaultTrue()
                ->end()
                ->booleanNode('use_write_actions_default')
                    ->defaultTrue()
                ->end()
                ->booleanNode('ask_controller_subdirectory')
                    ->defaultTrue()
                ->end()
                ->scalarNode('controller_subdirectory_default')
                    ->defaultNull()
                ->end()
                ->scalarNode('templates_directory')
                    ->defaultNull()
                ->end()
                ->scalarNode('templates_file_extension')
                    ->defaultValue('html.twig')
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
