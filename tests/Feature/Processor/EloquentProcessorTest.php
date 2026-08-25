<?php

namespace Worksome\DataExport\Tests\Feature\Processor;

use Illuminate\Support\Facades\DB;
use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Tests\Factories\UserFactory;
use Worksome\DataExport\Tests\Fake\FakeProcessorDriver;
use Worksome\DataExport\Tests\Fake\FakeProcessorWithOptionalDriver;
use Worksome\DataExport\Tests\Fake\FakeProcessorWithoutColumnsDriver;
use Worksome\DataExport\Tests\Fake\FakeProcessorWithRelationDriver;
use Worksome\DataExport\Tests\Fake\Models\Team;

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
    $processor = new FakeProcessorWithOptionalDriver();
    $processedData = $processor->process($export);
    $data = $processedData->getData();

    foreach ($data as $item) {
        $this->assertArrayHasKey('Compliance UK', $item);
        $this->assertArrayHasKey('Compliance US', $item);
    }

    expect($data[0])->toBe([
        'User ID' => '1',
        'name' => 'User One',
        'Is Admin' => '1',
        'Compliance UK' => 'applies',
        'Compliance US' => 'none',
    ]);

    expect($data[1])->toBe([
        'User ID' => '2',
        'name' => 'User Two',
        'Is Admin' => '0',
        'Compliance UK' => 'applies',
        'Compliance US' => 'none',
    ]);
});

it('does not serialise relations that are not exported', function () {
    createUsersInTeams();

    $queries = [];
    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });

    $data = (new FakeProcessorWithRelationDriver())->process(new Export())->getData();

    // One query for the users, one for the eagerly loaded teams. Serialising a
    // team would query its members on top of that.
    expect($queries)->toHaveCount(2);

    expect($data)->toBe([
        ['User ID' => '1', 'name' => 'User One', 'Team' => 'Team One'],
        ['User ID' => '2', 'name' => 'User Two', 'Team' => 'Team Two'],
    ]);
});

it('does not read model attributes when the processor declares no columns', function () {
    createUsersInTeams();

    $queries = [];
    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });

    $data = (new FakeProcessorWithoutColumnsDriver())->process(new Export())->getData();

    expect($queries)->toHaveCount(2);

    expect($data)->toBe([
        ['User ID' => '1', 'Team' => 'Team One'],
        ['User ID' => '2', 'Team' => 'Team Two'],
    ]);
});

function createUsersInTeams(): void
{
    UserFactory::new()->create([
        'name' => 'User One',
        'team_id' => Team::forceCreate(['name' => 'Team One'])->id,
    ]);

    UserFactory::new()->create([
        'name' => 'User Two',
        'team_id' => Team::forceCreate(['name' => 'Team Two'])->id,
    ]);
}
