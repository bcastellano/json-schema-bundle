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
     * Convert controller name to a route for schema file
     *
     * @param $controller
     * @return mixed|string
     */
    private function parseControllerName($controller)
    {
        if (false !== strpos($controller, '::')) {
            list($class, $method) = explode('::', $controller);

            $parts = explode('\\', $class);
            $name = end($parts) . '/' . $method;
        } else {

            $name = str_replace(':', '/', $controller);
        }

        return $name;
    }

    /**
     * @inheritdoc
     */
    public function getRequestSchemaFile(Request $request)
    {
        return sprintf("%s/request/%s.json", $this->resourcesDir, $this->parseControllerName($request->attributes->get('_controller')));
    }

    /**
     * @inheritdoc
     */
    public function getResponseSchemaFile(Request $request, Response $response)
    {
        return sprintf("%s/response/%s.json", $this->resourcesDir, $this->parseControllerName($request->attributes->get('_controller')));
    }
}
