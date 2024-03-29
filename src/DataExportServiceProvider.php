<?php

namespace Worksome\DataExport;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Events\BuildSchemaString;
use Nuwave\Lighthouse\Schema\Source\SchemaStitcher;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Schema\Types\LaravelEnumType;
use Worksome\DataExport\Enums\ExportResponseStatus;
use Worksome\DataExport\Enums\GeneratorType;
use Worksome\DataExport\Generator\GeneratorManager;
use Worksome\DataExport\GraphQL\Contracts\ExportValidator;
use Worksome\DataExport\GraphQL\NullExportValidator;
use Worksome\DataExport\Processor\ProcessorRepository;

class DataExportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GeneratorManager::class);
        $this->app->singleton(ProcessorRepository::class);
        $this->app->bind(ExportValidator::class, NullExportValidator::class);
    }

    public function boot(): void
    {
        $this->registerMigrations();
        $this->callAfterResolving(Dispatcher::class, function (Dispatcher $dispatcher) {
            $this->buildQraphQLSchema($dispatcher);
        });
        $this->callAfterResolving(TypeRegistry::class, function (TypeRegistry $typeRegistry) {
            $this->registerGraphQLTypes($typeRegistry);
        });
    }

    private function registerMigrations(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    private function buildQraphQLSchema(Dispatcher $dispatcher): void
    {
        $dispatcher->listen(BuildSchemaString::class, function (): string {
            $stitcher = new SchemaStitcher(__DIR__ . '/../GraphQL/schema.graphql');

            return $stitcher->getSchemaString();
        });
    }

    private function registerGraphQLTypes(TypeRegistry $typeRegistry): void
    {
        $types = [
            ExportResponseStatus::class,
            GeneratorType::class,
        ];

        foreach ($types as $type) {
            $typeRegistry->register(new LaravelEnumType($type));
        }
    }
}
