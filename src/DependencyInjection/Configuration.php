<?php

namespace Bcastellano\JsonSchemaBundle\DependencyInjection;

use Bcastellano\JsonSchemaBundle\Locator\ControllerSchemaFileLocator;
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
                ->arrayNode('validator')
                    ->info('Configuration for validation services')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')
                            ->cannotBeEmpty()
                            ->defaultValue(JsonSchemaValidator::class)
                        ->end()
                        ->booleanNode('use_listener')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('locator')
                    ->info('Configuration about Json-Schema files location')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')
                            ->cannotBeEmpty()
                            ->defaultValue(ControllerSchemaFileLocator::class)
                        ->end()
                        ->scalarNode('resources_dir')
                            ->cannotBeEmpty()
                            ->defaultValue('%kernel.root_dir%/Resources/Schemas')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('generator')
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
