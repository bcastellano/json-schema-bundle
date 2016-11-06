<?php

namespace Bcastellano\JsonSchemaBundle\Tests\Validator;

use Bcastellano\JsonSchemaBundle\Generator\SchemaFileGeneratorInterface;
use Bcastellano\JsonSchemaBundle\Tests\TestHiddenFunctionsTrait;
use Bcastellano\JsonSchemaBundle\Validator\JsonRpcSchemaValidator;
use Bcastellano\JsonSchemaBundle\Validator\JsonSchemaValidatorInterface;
use JsonSchema\Constraints\ConstraintInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonRpcSchemaValidatorTest extends \PHPUnit_Framework_TestCase
{
    use TestHiddenFunctionsTrait;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $baseValidator;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $schemaFileGenerator;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var string Json body to validate */
    private $json = '{}';
    /** @var string Json schema file path */
    private $schemaFile = 'file.json';

    public function setUp()
    {
        $this->baseValidator = $this->getMockBuilder(ConstraintInterface::class)->getMock();
        $this->schemaFileGenerator = $this->getMockBuilder(SchemaFileGeneratorInterface::class)->getMock();
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
    }

    public function tearDown()
    {
        $this->baseValidator = null;
        $this->schemaFileGenerator = null;
        $this->logger = null;
    }

    private function createValidatorInstance()
    {
        return new JsonRpcSchemaValidator(
            $this->baseValidator,
            '/some/dir',
            $this->schemaFileGenerator,
            $this->logger
        );
    }

    public function testInterface()
    {
        $validator = $this->createValidatorInstance();

        $this->assertInstanceOf(JsonSchemaValidatorInterface::class, $validator);
    }

    public function testValidateJsonSuccess()
    {
        $this->schemaFileGenerator->expects($this->once())
            ->method('generate')
            ->with($this->schemaFile, $this->json);

        $this->baseValidator->expects($this->once())
            ->method('check');

        $this->baseValidator->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->logger->expects($this->once())
            ->method('debug')
            ->with("Valid json file: $this->schemaFile");

        $validator = $this->createValidatorInstance();

        $this->assertTrue($validator->validateJson($this->json, $this->schemaFile));
    }

    public function testValidateJsonFailed()
    {
        $this->schemaFileGenerator->expects($this->once())
            ->method('generate')
            ->with($this->schemaFile, $this->json);

        $this->baseValidator->expects($this->once())
            ->method('check');

        $this->baseValidator->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $this->logger->expects($this->once())
            ->method('error')
            ->with("Invalid json file: $this->schemaFile");

        $validator = $this->createValidatorInstance();

        $this->assertFalse($validator->validateJson($this->json, $this->schemaFile));
    }

    /**
     * @expectedException \Bcastellano\JsonSchemaBundle\Exception\JsonSchemaFileNotFoundException
     */
    public function testValidateJsonSchemaFileNotFound()
    {
        $this->schemaFileGenerator = null;

        $this->baseValidator->expects($this->never())
            ->method('check');

        $validator = $this->createValidatorInstance();

        $this->assertFalse($validator->validateJson($this->json, $this->schemaFile));
    }

    /**
     * @expectedException \Bcastellano\JsonSchemaBundle\Exception\JsonSchemaValidationException
     */
    public function testValidateRequestBodyValidHeader()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->headers = $this->getMockBuilder(HeaderBag::class)->getMock();

        $request->headers->expects($this->once())
            ->method('get')
            ->with('Content-Type')
            ->willReturn('application/json');
        $request->expects($this->any())
            ->method('getContent');
        $this->baseValidator->expects($this->once())
            ->method('getErrors')
            ->willReturn([]);

        $validator = $this->createValidatorInstance();

        $validator->validateRequestBody($request);
    }

    public function testNotValidateRequestBodyInvalidHeader()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->headers = $this->getMockBuilder(HeaderBag::class)->getMock();
        $request->attributes = $this->getMockBuilder(ParameterBag::class)->getMock();

        $request->headers->expects($this->once())
            ->method('get')
            ->with('Content-Type')
            ->willReturn('text/html');
        $request->attributes->expects($this->never())->method('get');
        $this->baseValidator->expects($this->never())->method('getErrors');

        $validator = $this->createValidatorInstance();

        $validator->validateRequestBody($request);
    }

    /**
     * @expectedException \Bcastellano\JsonSchemaBundle\Exception\JsonSchemaValidationException
     */
    public function testValidateResponseBodyValidHeader()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $response = $this->getMockBuilder(Response::class)->getMock();
        $response->headers = $this->getMockBuilder(HeaderBag::class)->getMock();

        $response->headers->expects($this->once())
            ->method('get')
            ->with('Content-Type')
            ->willReturn('application/json');
        $request->expects($this->any())
            ->method('getContent');
        $this->baseValidator->expects($this->once())
            ->method('getErrors')
            ->willReturn([]);

        $validator = $this->createValidatorInstance();

        $validator->validateResponseBody($request, $response);
    }

    public function testNotValidateResponseBodyInvalidHeader()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->attributes = $this->getMockBuilder(ParameterBag::class)->getMock();
        $response = $this->getMockBuilder(Response::class)->getMock();
        $response->headers = $this->getMockBuilder(HeaderBag::class)->getMock();

        $response->headers->expects($this->once())
            ->method('get')
            ->with('Content-Type')
            ->willReturn('text/html');
        $request->attributes->expects($this->never())->method('get');
        $this->baseValidator->expects($this->never())->method('getErrors');

        $validator = $this->createValidatorInstance();

        $validator->validateResponseBody($request, $response);
    }

    public function testGetRequestSchemaFile()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();

        $request->expects($this->any())
            ->method('getContent')
            ->willReturn('{"method":"service/method"}');

        $validator = $this->createValidatorInstance();

        $actual = $this->invokeMethod($validator, 'getRequestSchemaFile', [$request]);

        $this->assertSame('/some/dir/request/service/method.json', $actual);
    }

    public function testGetResponseSchemaFile()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();

        $request->expects($this->any())
            ->method('getContent')
            ->willReturn('{"method":"service/method"}');

        $validator = $this->createValidatorInstance();

        $actual = $this->invokeMethod($validator, 'getResponseSchemaFile', [$request]);

        $this->assertSame('/some/dir/response/service/method.json', $actual);
    }
}
