<?php

namespace Bcastellano\JsonSchemaBundle;

use Bcastellano\JsonSchemaBundle\DependencyInjection\JsonSchemaExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class JsonSchemaBundle extends Bundle
{
    /**
     * Return bundle extension
     *
     * @return JsonSchemaExtension
     */
    public function getContainerExtension()
    {
        return new JsonSchemaExtension();
    }

    /**
     * Register Commands.
     *
     * Disabled as commands are registered as services.
     *
     * @param Application $application An Application instance
     */
    public function registerCommands(Application $application)
    {
        return;
    }
}
