<?php

namespace Worksome\DataExport;

use Illuminate\Support\ServiceProvider;
use Worksome\DataExport\Delivery\Contracts\Delivery;
use Worksome\DataExport\Delivery\DeliveryManager;
use Worksome\DataExport\Generator\Contracts\Generator;
use Worksome\DataExport\Generator\GeneratorManager;
use Illuminate\Contracts\Events\Dispatcher;

class DataExportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Generator::class, GeneratorManager::class);
        $this->app->bind(Delivery::class, DeliveryManager::class);
    }

    public function boot(Dispatcher $dispatcher): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $dispatcher->listen(
            \Nuwave\Lighthouse\Events\BuildSchemaString::class,
            function(): string {
                $stitcher = new \Nuwave\Lighthouse\Schema\Source\SchemaStitcher(__DIR__ . '/../GraphQL/schema.graphql');
                return $stitcher->getSchemaString();
            }
        );

    }
}
