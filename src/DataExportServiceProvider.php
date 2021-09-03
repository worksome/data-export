<?php

namespace Worksome\DataExport;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Events\BuildSchemaString;
use Nuwave\Lighthouse\Schema\Source\SchemaStitcher;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Schema\Types\LaravelEnumType;
use Worksome\DataExport\Delivery\DeliveryManager;
use Worksome\DataExport\Enums\DeliveryType;
use Worksome\DataExport\Enums\ExportResponseStatus;
use Worksome\DataExport\Enums\GeneratorType;
use Worksome\DataExport\Generator\GeneratorManager;
use Worksome\DataExport\Processor\ProcessorRepository;

class DataExportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DeliveryManager::class);
        $this->app->singleton(GeneratorManager::class);
        $this->app->singleton(ProcessorRepository::class);
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
        $types = [
            ExportResponseStatus::class,
            DeliveryType::class,
            GeneratorType::class,
        ];

        foreach ($types as $type) {
            $typeRegistry->register(new LaravelEnumType($type));
        }
    }
}
