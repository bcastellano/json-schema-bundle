<?php

namespace Bcastellano\JsonSchemaBundle\Locator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface SchemaFileLocatorInterface
{
    /**
     * @param Request $request
     * @return string
     */
    public function getRequestSchemaFile(Request $request);

    /**
     * @param Request $request
     * @param Response $response
     * @return string
     */
    public function getResponseSchemaFile(Request $request, Response $response);
}
