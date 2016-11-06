<?php

namespace Bcastellano\JsonSchemaBundle\Validator;

use Bcastellano\JsonSchemaBundle\Exception\JsonSchemaFileNotFoundException;
use Bcastellano\JsonSchemaBundle\Exception\JsonSchemaValidationException;
use Bcastellano\JsonSchemaBundle\Generator\SchemaFileGeneratorInterface;
use Bcastellano\JsonSchemaBundle\Locator\SchemaFileLocatorInterface;
use JsonSchema\Constraints\ConstraintInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonSchemaValidator implements JsonSchemaValidatorInterface
{
    /** @var ConstraintInterface */
    protected $validator;

    /** @var SchemaFileLocatorInterface */
    protected $schemaFileLocator;

    /** @var SchemaFileGeneratorInterface|null */
    protected $schemaFileGenerator;

    /** @var LoggerInterface|null */
    protected $logger;

    /**
     * JsonSchemaValidator constructor.
     *
     * @param ConstraintInterface $validator
     * @param SchemaFileLocatorInterface $schemaFileLocator
     * @param SchemaFileGeneratorInterface|null $schemaFileGenerator
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ConstraintInterface $validator,
        SchemaFileLocatorInterface $schemaFileLocator,
        SchemaFileGeneratorInterface $schemaFileGenerator = null,
        LoggerInterface $logger = null
    ){
        $this->validator = $validator;
        $this->schemaFileLocator = $schemaFileLocator;
        $this->schemaFileGenerator = $schemaFileGenerator;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function validateJson($json, $schemaFile)
    {
        if (!file_exists($schemaFile)) {
            if ($this->schemaFileGenerator) {
                $this->schemaFileGenerator->generate($schemaFile, $json);
            } else {
                throw new JsonSchemaFileNotFoundException($schemaFile);
            }
        }

        // check if json validates against schemaFile
        $this->validator->check(json_decode($json), (object)['$ref' => 'file://' . $schemaFile]);

        if (! $valid=$this->validator->isValid()) {
            if ($this->logger) {
                $this->logger->error("Invalid json file: $schemaFile");
            }
        } else {
            if ($this->logger) {
                $this->logger->debug("Valid json file: $schemaFile");
            }
        }

        return $valid;
    }

    /**
     * @inheritdoc
     */
    public function validateRequestBody(Request $request)
    {
        if (0 !== strpos($request->headers->get('Content-Type'), 'application/json')) {
            return;
        }

        $schemaFilePath = $this->schemaFileLocator->getRequestSchemaFile($request);

        if (! $this->validateJson($request->getContent(), $schemaFilePath)) {
            throw new JsonSchemaValidationException($this->validator->getErrors());
        }
    }

    /**
     * @inheritdoc
     */
    public function validateResponseBody(Request $request, Response $response)
    {
        if (0 !== strpos($response->headers->get('Content-Type'), 'application/json')) {
            return;
        }

        $schemaFilePath = $this->schemaFileLocator->getResponseSchemaFile($request, $response);

        if (! $this->validateJson($response->getContent(), $schemaFilePath)) {
            throw new JsonSchemaValidationException($this->validator->getErrors());
        }
    }
}
