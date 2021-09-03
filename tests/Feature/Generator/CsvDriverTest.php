<?php

namespace Worksome\DataExport\Tests\Feature\Generator;

use Illuminate\Support\Facades\Storage;
use Worksome\DataExport\Generator\CsvDriver;
use Worksome\DataExport\Generator\GeneratorFile;
use Worksome\DataExport\Processor\ProcessorData;

it('can export data to csv format', function () {
    $data = [
        ['name' => 'John Doe'],
        ['name' => 'Jane Doe'],
    ];

    $processorData = new ProcessorData($data);

    $csvDriver = new CsvDriver();

    expect($csvDriver->exportToCsv($processorData))->toBe("name\r\nJohn Doe\r\nJane Doe\r\n");
});

it('can save csv on storage', function (){
    Storage::fake();

    $data = [
        ['name' => 'John Doe'],
        ['name' => 'Jane Doe'],
    ];

    $processorData = new ProcessorData($data);

    $csvDriver = new CsvDriver();

    $contents = $csvDriver->exportToCsv($processorData);
    $savedFile = $csvDriver->saveToStorage('foo.csv', $contents, $processorData);

    Storage::assertExists($savedFile->getPath());

    expect($savedFile)->toBeInstanceOf(GeneratorFile::class);
});

it('can fully generate the csv file', function () {
    Storage::fake('default');

    $data = [
        ['name' => 'John Doe'],
        ['name' => 'Jane Doe'],
    ];

    $processorData = new ProcessorData($data);

    $csvDriver = new CsvDriver();

    $savedFile = $csvDriver->generate($processorData);

    Storage::assertExists($savedFile->getPath());

    expect($savedFile)->toBeInstanceOf(GeneratorFile::class);
});
