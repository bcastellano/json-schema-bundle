<?php

namespace Bcastellano\JsonSchemaBundle\Locator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonRpcSchemaFileLocator implements SchemaFileLocatorInterface
{
    protected $resourcesDir;

    public function __construct($resourcesDir)
    {
        $this->resourcesDir = $resourcesDir;
    }

    /**
     * Get method name from request content
     *
     * @param Request $request
     * @return mixed
     */
    private function getMethod(Request $request)
    {
        static $method;

        if (null == $method) {
            $json = json_decode($request->getContent(), true);

            if (isset($json['method'])) {
                $method = $json['method'];
            }
        }

        return $method;
    }

    /**
     * @inheritdoc
     */
    public function getRequestSchemaFile(Request $request)
    {
        return sprintf("%s/request/%s.json", $this->resourcesDir, $this->getMethod($request));
    }

    /**
     * @inheritdoc
     */
    public function getResponseSchemaFile(Request $request, Response $response)
    {
        return sprintf("%s/response/%s.json", $this->resourcesDir, $this->getMethod($request));
    }
}
