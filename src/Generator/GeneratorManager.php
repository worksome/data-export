<?php

namespace Worksome\DataExport\Generator;

use Illuminate\Support\Manager;
use Worksome\DataExport\Generator\Contracts\GeneratorDriver as GeneratorDriverContract;

class GeneratorManager extends Manager
{
    public function createCsvDriver(): GeneratorDriverContract
    {
        return new CsvDriver();
    }

    public function getDefaultDriver(): string
    {
        return 'csv';
    }
}
