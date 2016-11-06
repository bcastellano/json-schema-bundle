<?php

namespace Bcastellano\JsonSchemaBundle\Tests\DependencyInjection;

use Bcastellano\JsonSchemaBundle\DependencyInjection\JsonSchemaExtension;
use Bcastellano\JsonSchemaBundle\Validator\JsonSchemaValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JsonSchemaExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerBuilder */
    private $container;

    /** @var JsonSchemaExtension */
    private $extension;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new JsonSchemaExtension();
    }

    protected function tearDown()
    {
        unset(
            $this->container,
            $this->extension
        );
    }

    public function testDefaults()
    {
        $this->extension->load(['json_schema' => ['resources_dir' => '/some/dir']], $this->container);

        $this->assertContainerHasDefinition('json_schema.base_validator');
        $this->assertContainerNotHasDefinition('json_schema.file_generator');
        $this->assertContainerHasDefinition('json_schema.validator');
        $this->assertContainerHasDefinition('json_schema.validator.subscriber');
        $this->assertContainerParameter('/some/dir', 'json_schema.resources_dir');
        $this->assertContainerParameter(JsonSchemaValidator::class, 'json_schema.validator.class');

        $this->assertFalse($this->container->getDefinition('json_schema.base_validator')->isPublic());
        $this->assertTrue($this->container->getDefinition('json_schema.validator.subscriber')->hasTag('kernel.event_subscriber'));
        $this->assertNull($this->container->getDefinition('json_schema.validator')->getArgument(2));
        $this->assertEquals($this->container->getDefinition('json_schema.validator')->getClass(), '%json_schema.validator.class%');
    }

    public function testDisableSubscriber()
    {
        $this->extension->load(['json_schema' => ['validator' => ['use_listener' => false]]], $this->container);

        $this->assertContainerNotHasDefinition('json_schema.validator.subscriber');
    }

    public function testSchemaGeneratorCommand()
    {
        $this->extension->load(['json_schema' => ['schema_generator' => ['command' => '/path/to/cmd']]], $this->container);

        $def = $this->container->getDefinition('json_schema.file_generator');

        $this->assertFalse($def->isPublic());
        $this->assertEquals(['/path/to/cmd'], $def->getArguments());
    }

    public function testSchemaGeneratorService()
    {
        $this->extension->load(['json_schema' => ['schema_generator' => ['service' => 'some.service.id']]], $this->container);

        $id = 'json_schema.file_generator';
        $this->assertFalse($this->container->hasDefinition($id));
        $this->assertTrue($this->container->hasAlias($id));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid configuration values for json_schema.schema_generator
     */
    public function testSchemaGeneratorInvalid()
    {
        $this->extension->load(['json_schema' => ['schema_generator' => []]], $this->container);
    }

    /**
     * @param mixed  $value
     * @param string $key
     */
    private function assertContainerParameter($value, $key)
    {
        $this->assertSame($value, $this->container->getParameter($key));
    }
    /**
     * @param string $id
     */
    private function assertContainerHasDefinition($id)
    {
        $this->assertTrue(($this->container->hasDefinition($id) ?: $this->container->hasAlias($id)));
    }
    /**
     * @param string $id
     */
    private function assertContainerNotHasDefinition($id)
    {
        $this->assertFalse(($this->container->hasDefinition($id) ?: $this->container->hasAlias($id)));
    }
}
