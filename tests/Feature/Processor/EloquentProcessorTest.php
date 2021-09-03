<?php

namespace Worksome\DataExport\Tests\Feature\Processor;

use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Tests\Factories\UserFactory;
use Worksome\DataExport\Tests\Fake\FakeProcessorDriver;

it('can process a query that returns no results', function () {
    $export = new Export();

    $processor = new FakeProcessorDriver();

    $processedData = $processor->process($export);

    $data = $processedData->getData();

    expect($data)->toBeEmpty();
});

it('can process a query that returns results with filtered columns', function () {
    UserFactory::new()->times(10)->create();

    $export = new Export();

    $processor = new FakeProcessorDriver();

    $processedData = $processor->process($export);

    $data = $processedData->getData();

    expect($data)->not->toBeEmpty();

    expect(array_keys($data[0]))->toBe([
        'id',
        'name',
    ]);
});
