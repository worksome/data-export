<?php

namespace Worksome\DataExport;

use Illuminate\Support\ServiceProvider;
use Worksome\DataExport\Delivery\Contracts\Delivery;
use Worksome\DataExport\Delivery\DeliveryManager;
use Worksome\DataExport\Generator\Contracts\Generator;
use Worksome\DataExport\Generator\GeneratorManager;
use Worksome\DataExport\Processor\Contracts\Processor;
use Worksome\DataExport\Processor\ProcessorManager;

class DataExportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Processor::class, ProcessorManager::class);
        $this->app->bind(Generator::class, GeneratorManager::class);
        $this->app->bind(Delivery::class, DeliveryManager::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '../database/migrations');
    }
}
