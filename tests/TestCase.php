<?php

namespace Worksome\DataExport\Tests;

use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Worksome\DataExport\DataExportServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'ws-testing'])->run();

        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->boolean('is_admin')->default(0);
            $table->timestamps();
        });
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'ws-testing');
        $app['config']->set('database.connections.ws-testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [DataExportServiceProvider::class];
    }
}
