<?php

namespace Bcastellano\JsonSchemaBundle\Generator;

interface SchemaFileGeneratorInterface
{
    /**
     * @param string $file Filename for schema file
     * @param string $json Base json data to create schema
     */
    public function generate($file, $json);
}