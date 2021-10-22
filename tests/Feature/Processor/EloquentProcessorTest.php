<?php

namespace Worksome\DataExport\Tests\Feature\Processor;

use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Tests\Factories\UserFactory;
use Worksome\DataExport\Tests\Fake\FakeProcessorDriver;
use Worksome\DataExport\Tests\Fake\FakeProcessorWithComplianceDriver;

it('can process a query that returns no results', function () {
    $export = new Export();

    $processor = new FakeProcessorDriver();

    $processedData = $processor->process($export);

    $data = $processedData->getData();

    expect($data)->toBeEmpty();
});

it('can process a query that returns results with filtered columns', function () {
    UserFactory::new()->create([
        'name'     => 'User One',
        'is_admin' => true,
    ]);

    UserFactory::new()->create([
        'name'     => 'User Two',
        'is_admin' => false,
    ]);

    $export = new Export();

    $processor = new FakeProcessorDriver();

    $processedData = $processor->process($export);

    $data = $processedData->getData();

    expect($data)->not->toBeEmpty();
    expect($data)->toHaveCount(2);

    expect($data[0])->toBe([
        'User ID'  => '1',
        'name'     => 'User One',
        'Is Admin' => '1',
    ]);
    expect($data[1])->toBe([
        'User ID'  => '2',
        'name'     => 'User Two',
        'Is Admin' => '0',
    ]);
});

it('should correctly process compliance data', function () {
    UserFactory::new()->create([
        'name'     => 'User One',
        'is_admin' => true,
    ]);
    UserFactory::new()->create([
        'name'     => 'User Two',
        'is_admin' => false,
    ]);

    $export = new Export();
    $processor = new FakeProcessorWithComplianceDriver();
    $processedData = $processor->process($export);
    $data = $processedData->getData();

    foreach ($data as $item) {
        $this->assertArrayHasKey('Compliance UK', $item);
        $this->assertArrayHasKey('Compliance US', $item);
    }

    expect($data[0])->toBe([
        "User ID" => "1",
        "name" => "User One",
        "Is Admin" => "1",
        "Compliance UK" => "applies",
        "Compliance US" => "none"
    ]);

    expect($data[1])->toBe([
        "User ID" => "2",
        "name" => "User Two",
        "Is Admin" => "0",
        "Compliance UK" => "applies",
        "Compliance US" => "none"
    ]);
});
