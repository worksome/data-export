<?php

namespace Worksome\DataExport;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Events\BuildSchemaString;
use Nuwave\Lighthouse\Schema\Source\SchemaStitcher;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Schema\Types\LaravelEnumType;
use Worksome\DataExport\Delivery\Contracts\Delivery;
use Worksome\DataExport\Delivery\DeliveryManager;
use Worksome\DataExport\Enums\DeliveryType;
use Worksome\DataExport\Enums\ExportResonseStatus;
use Worksome\DataExport\Enums\ExportType;
use Worksome\DataExport\Enums\GeneratorType;
use Worksome\DataExport\Generator\Contracts\Generator;
use Worksome\DataExport\Generator\GeneratorManager;

class DataExportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Generator::class, GeneratorManager::class);
        $this->app->bind(Delivery::class, DeliveryManager::class);
    }

    public function boot(Dispatcher $dispatcher, TypeRegistry $typeRegistry): void
    {
        $this->registerMigrations();
        $this->buildQraphQLSchema($dispatcher);
        $this->registerGraphQLTypes($typeRegistry);
    }

    private function registerMigrations(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    private function buildQraphQLSchema(Dispatcher $dispatcher): void
    {
        $dispatcher->listen(BuildSchemaString::class, function(): string {
            $stitcher = new SchemaStitcher(__DIR__ . '/../GraphQL/schema.graphql');

            return $stitcher->getSchemaString();
        });
    }

    private function registerGraphQLTypes(TypeRegistry $typeRegistry): void
    {
        $types = collect([
            ExportType::class,
            ExportResonseStatus::class,
            DeliveryType::class,
            GeneratorType::class,
        ]);

        $types->each(fn($type) => $typeRegistry->register(new LaravelEnumType($type)));
    }
}
