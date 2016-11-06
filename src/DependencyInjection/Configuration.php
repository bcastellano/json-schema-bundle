<?php

namespace Bcastellano\JsonSchemaBundle\DependencyInjection;

use Bcastellano\JsonSchemaBundle\Validator\JsonRpcSchemaValidator;
use Bcastellano\JsonSchemaBundle\Validator\JsonSchemaValidator;
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
                ->arrayNode('validator')
                    ->info('Configuration for validation services')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('class')
                            ->cannotBeEmpty()
                            ->defaultValue(JsonSchemaValidator::class)
                            ->values([JsonSchemaValidator::class, JsonRpcSchemaValidator::class])
                        ->end()
                        ->booleanNode('use_listener')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('schema_generator')
                    ->info('Configuration about Json-Schema files auto generation')
                    ->canBeEnabled()
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
