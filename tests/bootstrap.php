<?php

$loader = @include __DIR__ . '/../vendor/autoload.php';

if (!$loader) {
    die(<<<'EOT'

IMPORTANT: You must set up the project dependencies, run "composer install"


EOT
    );
}