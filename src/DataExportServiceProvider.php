<?php

namespace Worksome\DataExport;

use Illuminate\Support\ServiceProvider;
use Worksome\DataExport\Delivery\Contracts\Delivery;
use Worksome\DataExport\Delivery\DeliveryManager;
use Worksome\DataExport\Generator\Contracts\Generator;
use Worksome\DataExport\Generator\GeneratorManager;
use Illuminate\Contracts\Events\Dispatcher;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Schema\Types\LaravelEnumType;
use Worksome\DataExport\Enums\DeliveryType;
use Worksome\DataExport\Enums\ExportResonseStatus;
use Worksome\DataExport\Enums\ExportType;
use Worksome\DataExport\Enums\GeneratorType;

class DataExportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Generator::class, GeneratorManager::class);
        $this->app->singleton(Delivery::class, DeliveryManager::class);
    }

    public function boot(Dispatcher $dispatcher, TypeRegistry $typeRegistry): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $dispatcher->listen(
            \Nuwave\Lighthouse\Events\BuildSchemaString::class,
            function(): string {
                $stitcher = new \Nuwave\Lighthouse\Schema\Source\SchemaStitcher(__DIR__ . '/../GraphQL/schema.graphql');
                return $stitcher->getSchemaString();
            }
        );

        $types = collect([
            ExportType::class,
            ExportResonseStatus::class,
            DeliveryType::class,
            GeneratorType::class,
        ]);

        $types->each(fn($type) => $typeRegistry->register(new LaravelEnumType($type)));
    }
}
