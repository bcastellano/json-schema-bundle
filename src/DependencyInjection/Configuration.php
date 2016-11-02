<?php

namespace Bcastellano\JsonSchemaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('json_schema');

        $rootNode->children()
                ->scalarNode('resources_dir')
                    ->defaultValue('%kernel.root_dir%/../src/Resources/Schemas')
                ->end()
                ->booleanNode('use_listener')
                    ->defaultTrue()
                ->end()
                ->arrayNode('schema_generator')
                    ->children()
                        ->scalarNode('command')->cannotBeEmpty()->end()
                        ->scalarNode('service')->cannotBeEmpty()->end()
                    ->end()
                    ->validate()
                        ->always(function ($v) {
                            if (isset($v['command']) && isset($v['service'])) {
                                throw new \InvalidArgumentException('"command" and "service" could not be used together.');
                            }
                            return $v;
                        })
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
