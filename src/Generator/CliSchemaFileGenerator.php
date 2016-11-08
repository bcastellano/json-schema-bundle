<?php

namespace Bcastellano\JsonSchemaBundle\Generator;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CliSchemaFileGenerator implements SchemaFileGeneratorInterface
{
    protected $command;

    /**
     * CliSchemaFileGenerator constructor.
     *
     * @param string $command Command string to generate schema file.
     * There are 2 variables to substitute {{source_file}} and {{target_file}}
     * which are source json file name and schema to generate file name respectively
     *
     * example: "/path/to/executable {{source_file}} --output {{target_file}}"
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * @inheritdoc
     */
    public function generate($file, $json)
    {
        $fs = new Filesystem();

        // generate temp file
        if (false === ($tmpJson = tempnam(sys_get_temp_dir(), basename($file)))) {
            throw new IOException('A temporary file could not be created.');
        }

        $fs->dumpFile($tmpJson, $json);

        // substitute parameters
        $command = str_replace(['{{source_file}}', '{{target_file}}'], [$tmpJson, $file], $this->command);

        // exec command
        $process = new Process($command);
        $exitCode = $process->run();

        $fs->remove($tmpJson);

        if ($exitCode !== 0) {
            throw new ProcessFailedException($process);
        }
    }
}
