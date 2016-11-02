<?php

namespace Bcastellano\JsonSchemaBundle\Validator;

use Bcastellano\JsonSchemaBundle\Exception\JsonSchemaValidationException;
use Bcastellano\JsonSchemaBundle\Generator\SchemaFileGeneratorInterface;
use JsonSchema\Constraints\ConstraintInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonSchemaValidator implements JsonSchemaValidatorInterface
{
    protected $resourcesDir;
    protected $validator;
    protected $schemaFileGenerator;
    protected $logger;

    public function __construct(
        ConstraintInterface $validator,
        SchemaFileGeneratorInterface $schemaFileGenerator,
        $resourcesDir,
        LoggerInterface $logger = null
    ){
        $this->validator = $validator;
        $this->schemaFileGenerator = $schemaFileGenerator;
        $this->resourcesDir = $resourcesDir;
        $this->logger = $logger;
    }

    public function validateJson($json, $schemaFile)
    {
        if (!file_exists($schemaFile)) {
            $this->schemaFileGenerator->generate($schemaFile, $json);
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

    protected function getRequestSchemaFile(Request $request)
    {
        return sprintf("%s/request/%s.json", $this->resourcesDir, str_replace(':', '/', $request->attributes->get('_controller')));
    }

    protected function getResponseSchemaFile(Request $request)
    {
        return sprintf("%s/response/%s.json", $this->resourcesDir, str_replace(':', '/', $request->attributes->get('_controller')));
    }

    /**
     * @inheritdoc
     */
    public function validateRequestBody(Request $request)
    {
        if (0 !== strpos($request->headers->get('Content-Type'), 'application/json')) {
            return;
        }

        $schemaFilePath = $this->getRequestSchemaFile($request);

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

        $schemaFilePath = $this->getResponseSchemaFile($request);

        if (! $this->validateJson($response->getContent(), $schemaFilePath)) {
            throw new JsonSchemaValidationException($this->validator->getErrors());
        }
    }
}
