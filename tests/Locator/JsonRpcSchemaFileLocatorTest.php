<?php

namespace Bcastellano\JsonSchemaBundle\Tests\Locator;

use Bcastellano\JsonSchemaBundle\Locator\JsonRpcSchemaFileLocator;
use Bcastellano\JsonSchemaBundle\Locator\SchemaFileLocatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonRpcSchemaFileLocatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var JsonRpcSchemaFileLocator */
    private $locator;

    public function setUp()
    {
        $this->locator = new JsonRpcSchemaFileLocator('/resources/dir');
    }

    public function tearDown()
    {
        $this->locator = null;
    }

    public function testInterface()
    {
        $this->assertInstanceOf(SchemaFileLocatorInterface::class, $this->locator);
    }

    public function testGetRequestSchemaFile()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();

        $request->expects($this->any())
            ->method('getContent')
            ->willReturn('{"method":"service/action"}');

        $actual = $this->locator->getRequestSchemaFile($request);

        $this->assertSame('/resources/dir/request/service/action.json', $actual);
    }

    public function testGetResponseSchemaFile()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $response = $this->getMockBuilder(Response::class)->getMock();

        $request->expects($this->any())
            ->method('getContent')
            ->willReturn('{"method":"service/action"}');

        $actual = $this->locator->getResponseSchemaFile($request, $response);

        $this->assertSame('/resources/dir/response/service/action.json', $actual);
    }
}
