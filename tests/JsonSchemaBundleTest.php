<?php

namespace Bcastellano\JsonSchemaBundle\Tests;

use Bcastellano\JsonSchemaBundle\DependencyInjection\JsonSchemaExtension;
use Bcastellano\JsonSchemaBundle\JsonSchemaBundle;

class JsonSchemaBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testExtensionClass()
    {
        $bundle = new JsonSchemaBundle();

        $this->assertInstanceOf(JsonSchemaExtension::class, $bundle->getContainerExtension());
    }
}