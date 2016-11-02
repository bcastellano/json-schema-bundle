<?php

namespace Bcastellano\JsonSchemaBundle\Tests\DependencyInjection;

use Bcastellano\JsonSchemaBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfig()
    {
        $defaultConfig = [
            'resources_dir' => '%kernel.root_dir%/../src/Resources/Schemas',
            'use_listener' => true
        ];

        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, []);

        $this->assertInstanceOf(ConfigurationInterface::class, $configuration);
        $this->assertInstanceOf(TreeBuilder::class, $configuration->getConfigTreeBuilder());
        $this->assertEquals($defaultConfig, $config);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSchemaGeneratorIncompatibleConfig()
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $processor->processConfiguration($configuration, ['json_schema' => ['schema_generator' => ['command' => 'cmd',  'service' => 'some.service.name']]]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSchemaGeneratorCommandNotEmpty()
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $processor->processConfiguration($configuration, ['json_schema' => ['schema_generator' => ['command' => '']]]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSchemaGeneratorServiceNotEmpty()
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $processor->processConfiguration($configuration, ['json_schema' => ['schema_generator' => ['service' => '']]]);
    }
}