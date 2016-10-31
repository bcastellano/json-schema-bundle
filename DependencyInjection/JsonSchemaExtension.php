<?php

namespace Bcastellano\JsonSchemaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JsonSchemaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // add configuration as parameters
        $container->setParameter($this->getAlias().'.resources_dir', $config['resources_dir']);

        // remove subscriber
        if (false == $config['use_listener']) {
            $container->removeDefinition('json_schema.validator.subscriber');
        }

        // configure schema file generator service
        if (isset($config['schema_generator'])) {
            switch (true) {
                case isset($config['schema_generator']['command']):
                    $def = $container->getDefinition('json_schema.file_generator');
                    $def->addArgument($config['schema_generator']['command']);
                    break;
                case isset($config['schema_generator']['service']):
                    $container->removeDefinition('json_schema.file_generator');
                    $container->setAlias('json_schema.file_generator', $config['schema_generator']['service']);
                    break;
                default:
                    throw new \Exception('Invalid configuration values for json_schema.schema_generator');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return "json_schema";
    }
}