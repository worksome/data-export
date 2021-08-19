<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\PSR4\Rector\FileWithoutNamespace\NormalizeNamespaceByPSR4ComposerAutoloadRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Worksome\CodingStyle\WorksomeRectorConfig;

return static function (ContainerConfigurator $containerConfigurator): void {
    WorksomeRectorConfig::setup($containerConfigurator);

    // get parameters
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__ . '/app',
        __DIR__ . '/tests',
    ]);

    $parameters->set(Option::SKIP, [
        NormalizeNamespaceByPSR4ComposerAutoloadRector::class => [
            __DIR__ . '/app/helpers.php',
        ],
    ]);

    $parameters->set(Option::ENABLE_CACHE, true);
};
