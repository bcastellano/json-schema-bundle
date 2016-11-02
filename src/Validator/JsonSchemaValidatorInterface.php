<?php

namespace Bcastellano\JsonSchemaBundle\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface JsonSchemaValidatorInterface
{
    /**
     * @param string $json Json to validate
     * @param string $schemaFile Json schema file path
     * @return bool
     */
    public function validateJson($json, $schemaFile);

    /**
     * @param Request $request
     */
    public function validateRequestBody(Request $request);

    /**
     * @param Request $request
     * @param Response $response
     */
    public function validateResponseBody(Request $request, Response $response);
}
