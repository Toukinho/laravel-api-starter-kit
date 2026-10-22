<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([__DIR__.'/app', __DIR__.'/routes', __DIR__.'/database', __DIR__.'/tests'])
    ->withSkip([__DIR__.'/database/migrations'])
    ->withPhpSets()
    ->withPreparedSets(deadCode: true, codeQuality: true, typeDeclarations: true)
    ->withSets([LaravelSetList::LARAVEL_CODE_QUALITY, LaravelSetList::LARAVEL_COLLECTION]);
