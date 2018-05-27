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
                ->scalarNode('default_subdirectory')
                    ->defaultNull()
                ->end()
                ->booleanNode('ask_subdirectory')
                    ->defaultFalse()
                ->end()
            ->booleanNode('ask_display_field')
                ->defaultTrue()
            ->end()
            ->booleanNode('ask_traits')
                ->defaultTrue()
            ->end()
                ->arrayNode('trait_options')
                    ->scalarPrototype()
                        ->defaultNull()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
