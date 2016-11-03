[![Build Status](https://travis-ci.org/bcastellano/json-schema-bundle.svg?branch=master)](https://travis-ci.org/bcastellano/json-schema-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/12914036-bfdb-4ba1-b89a-9f0733e0a7a3/mini.png)](https://insight.sensiolabs.com/projects/12914036-bfdb-4ba1-b89a-9f0733e0a7a3)
[![License](http://img.shields.io/:license-mit-blue.svg)](http://doge.mit-license.org)

# Json-Schema Bundle
Symfony bundle to validate json api requests and responses based on [JSON Schema](http://json-schema.org) specification. 

It auto validates Requests and Responses for an API with json-schema files that can be auto generated from json body. 

JSON Schema is described in its specification as:

> JSON Schema is a JSON media type for defining the structure of JSON data. JSON Schema provides a contract for what JSON data is required for a given application and how to interact with it. JSON Schema is intended to define validation, documentation, hyperlink navigation, and interaction control of JSON data.

## Features

- Json Schema service for validate jsons
- Listener to auto validate request and responses
- Json Schema file generation from json body (request and responses content)

## Installation

Composer:

```bash
composer require bcastellano/json-schema-bundle
```

Load the bundle:

```php
<?php
// app/AppKernel.php

use Bcastellano\JsonSchemaBundle\JsonSchemaBundle;

public function registerBundles()
{
    $bundles = array(
        // ...
        new JsonSchemaBundle(),
    );
}
```

## Configuration
This is a complete example of configuration parameters:
```yaml
json_schema:
    # Directory for json schema files (also to save new ones)
    resources_dir: '%kernel.root_dir%/../src/Resources/Schemas'
    
    # To auto register request and response events to auto validate jsons 
    use_listener: true
    
    # Configuration for schema generator
    schema_generator:
        # NOTE: these two configurations are incompatible, you can't configure both
        # this use any external command to generate schema from json
        command: '/path/to/command {{source_file}} --output {{target_file}}'
        # this use any custom service to generate schema from json
        service: 'some.custom.service'
```

## License
This bundle is under the [MIT License](LICENSE)


