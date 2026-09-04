<?php

namespace Worksome\DataExport\Tests\Feature\Processor;

use Worksome\DataExport\Processor\ProcessorData;

it('can retrieve the data', function () {
    $processorData = new ProcessorData([
        'foo' => 'Bar',
    ], 'randomstuff');

    expect($processorData->rows())->toBe([
        'foo' => 'Bar',
    ]);

    expect($processorData->getType())->toBe('randomstuff');
});

it('streams rows and reports the columns and count for a row stream', function () {
    $rows = new \Worksome\DataExport\Processor\RowStream();
    $rows->push(['id' => '1'], [['UK' => 'applies']]);
    $rows->push(['id' => '2'], []);

    $processorData = new ProcessorData($rows, 'compliance');

    expect($processorData->getColumns())->toBe(['id', 'UK'])
        ->and($processorData->getCount())->toBe(2)
        ->and(iterator_to_array($processorData->rows(), false))->toBe([
            ['id' => '1', 'UK' => 'applies'],
            ['id' => '2', 'UK' => ''],
        ]);
});

it('derives the columns and count from a plain array of rows', function () {
    $processorData = new ProcessorData([
        ['a' => '1', 'b' => '2'],
        ['b' => '3', 'c' => '4'],
    ], 'shapes');

    expect($processorData->getColumns())->toBe(['a', 'b', 'c'])
        ->and($processorData->getCount())->toBe(2)
        ->and($processorData->rows())->toBe([
            ['a' => '1', 'b' => '2'],
            ['b' => '3', 'c' => '4'],
        ]);
});
