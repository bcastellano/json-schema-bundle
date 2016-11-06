<?php

namespace Bcastellano\JsonSchemaBundle\Validator;

use Symfony\Component\HttpFoundation\Request;

class JsonRpcSchemaValidator extends JsonSchemaValidator
{
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

    protected function getRequestSchemaFile(Request $request)
    {
        return sprintf("%s/request/%s.json", $this->resourcesDir, $this->getMethod($request));
    }

    protected function getResponseSchemaFile(Request $request)
    {
        return sprintf("%s/response/%s.json", $this->resourcesDir, $this->getMethod($request));
    }
}
