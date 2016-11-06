<?php

namespace Bcastellano\JsonSchemaBundle\Tests\DependencyInjection;

use Bcastellano\JsonSchemaBundle\DependencyInjection\Configuration;
use Bcastellano\JsonSchemaBundle\Locator\ControllerSchemaFileLocator;
use Bcastellano\JsonSchemaBundle\Validator\JsonSchemaValidator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfig()
    {
        $defaultConfig = [
            'validator' => [
                'class' => JsonSchemaValidator::class,
                'use_listener' => true
            ],
            'locator' => [
                'class' => ControllerSchemaFileLocator::class,
                'resources_dir' => '%kernel.root_dir%/Resources/Schemas'
            ],
            'generator' => [
                'enabled' => false
            ]
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
    public function testValidatorClassNotEmpty()
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $processor->processConfiguration($configuration, ['json_schema' => ['validator' => ['class' => '']]]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLocatorClassNotEmpty()
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $processor->processConfiguration($configuration, ['json_schema' => ['locator' => ['class' => '']]]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLocatorResourcesDirNotEmpty()
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $processor->processConfiguration($configuration, ['json_schema' => ['locator' => ['resources_dir' => '']]]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSchemaGeneratorIncompatibleConfig()
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $processor->processConfiguration($configuration, ['json_schema' => ['generator' => ['command' => 'cmd',  'service' => 'some.service.name']]]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSchemaGeneratorCommandNotEmpty()
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $processor->processConfiguration($configuration, ['json_schema' => ['generator' => ['command' => '']]]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSchemaGeneratorServiceNotEmpty()
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $processor->processConfiguration($configuration, ['json_schema' => ['generator' => ['service' => '']]]);
    }
}
