<?php

namespace Bcastellano\JsonSchemaBundle\Exception;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class JsonSchemaFileNotFoundException extends FileNotFoundException
{
    public function __construct($schemaFile, $message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous, $schemaFile);
    }
}
