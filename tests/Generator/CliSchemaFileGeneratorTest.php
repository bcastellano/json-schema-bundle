<?php

namespace Bcastellano\JsonSchemaBundle\Tests\Generator;

use Bcastellano\JsonSchemaBundle\Generator\CliSchemaFileGenerator;
use Bcastellano\JsonSchemaBundle\Generator\SchemaFileGeneratorInterface;

class CliSchemaFileGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $generator = new CliSchemaFileGenerator('/path/to/cmd');

        $this->assertInstanceOf(SchemaFileGeneratorInterface::class, $generator);
    }
}