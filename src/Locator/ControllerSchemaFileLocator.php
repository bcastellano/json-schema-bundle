<?php

namespace Bcastellano\JsonSchemaBundle\Locator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControllerSchemaFileLocator implements SchemaFileLocatorInterface
{
    protected $resourcesDir;

    public function __construct($resourcesDir)
    {
        $this->resourcesDir = $resourcesDir;
    }

    /**
     * @inheritdoc
     */
    public function getRequestSchemaFile(Request $request)
    {
        return sprintf("%s/request/%s.json", $this->resourcesDir, str_replace(':', '/', $request->attributes->get('_controller')));
    }

    /**
     * @inheritdoc
     */
    public function getResponseSchemaFile(Request $request, Response $response)
    {
        return sprintf("%s/response/%s.json", $this->resourcesDir, str_replace(':', '/', $request->attributes->get('_controller')));
    }
}
