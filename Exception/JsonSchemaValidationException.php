<?php

namespace Bcastellano\JsonSchemaBundle\Exception;

class JsonSchemaValidationException extends \RuntimeException
{
    private $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}