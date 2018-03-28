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
            ->end()
        ;
        return $treeBuilder;
    }
}
