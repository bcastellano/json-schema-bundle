<?php

namespace Bcastellano\JsonSchemaBundle\Tests\Locator;

use Bcastellano\JsonSchemaBundle\Locator\ControllerSchemaFileLocator;
use Bcastellano\JsonSchemaBundle\Locator\SchemaFileLocatorInterface;
use Bcastellano\JsonSchemaBundle\Tests\TestHiddenFunctionsTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControllerSchemaFileLocatorTest extends \PHPUnit_Framework_TestCase
{
    use TestHiddenFunctionsTrait;

    /** @var ControllerSchemaFileLocator */
    private $locator;

    public function setUp()
    {
        $this->locator = new ControllerSchemaFileLocator('/resources/dir');
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
        $request->attributes = $this->getMockBuilder(ParameterBag::class)->getMock();

        $request->attributes->expects($this->once())
            ->method('get')
            ->with('_controller')
            ->willReturn('Controller:Action');

        $actual = $this->locator->getRequestSchemaFile($request);

        $this->assertSame('/resources/dir/request/Controller/Action.json', $actual);
    }

    public function testGetResponseSchemaFile()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $response = $this->getMockBuilder(Response::class)->getMock();
        $request->attributes = $this->getMockBuilder(ParameterBag::class)->getMock();

        $request->attributes->expects($this->once())
            ->method('get')
            ->with('_controller')
            ->willReturn('Controller:Action');

        $actual = $this->locator->getResponseSchemaFile($request, $response);

        $this->assertSame('/resources/dir/response/Controller/Action.json', $actual);
    }
    
    public function testControllerNames()
    {
        $this->assertSame('kernel/actionExample', $this->invokeMethod($this->locator, 'parseControllerName', ['kernel:actionExample']));
        $this->assertSame('SomeBundle/Controller/ActionExample', $this->invokeMethod($this->locator, 'parseControllerName', ['SomeBundle:Controller:ActionExample']));
        $this->assertSame('ControllerName/ActionExample', $this->invokeMethod($this->locator, 'parseControllerName', ['SomeBundle\Controller\ControllerName::ActionExample']));
    }
}
