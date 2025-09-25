<?php

namespace Worksome\DataExport\Tests\Feature\GraphQL;

use Illuminate\Support\Facades\Event;
use Worksome\DataExport\Enums\ExportResponseStatus;
use Worksome\DataExport\Events\ExportInitialised;
use Worksome\DataExport\GraphQL\Mutations\CreateExport;
use Worksome\DataExport\GraphQL\NullExportValidator;
use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Services\CreateExport as CreateExportService;

it('can create an export', function () {
    Event::fake();

    $args = [
        'input' => [
            'userId'      => 1,
            'accountId'   => 1,
            'accountType' => 'user',
            'type'        => 'contract',
            'generatorType' => 'csv',
            'deliveries'    => [
                ['type' => 'email', 'value' => 'john@doe.com'],
            ],
            'args' => [
                'dateFrom' => '2021-01-01',
                'dateTo' => '2021-01-02',
            ],
        ],
    ];

    $service = new CreateExportService();
    $validator = new NullExportValidator();

    $response = (new CreateExport($service, $validator))->__invoke(null, $args);

    Event::assertDispatched(ExportInitialised::class);

    expect($response['status'])->toBe(ExportResponseStatus::Success);

    $export = Export::latest('created_at')->first()->refresh();

    expect($export->args)
        ->dateFrom->toBe('2021-01-01')
        ->dateTo->toBe('2021-01-02');
});

it('can exports with correct dates', function () {
    $args = [
        'input' => [
            'userId'      => 1,
            'accountId'   => 1,
            'accountType' => 'user',
            'type'        => 'contract',
            'generatorType' => 'csv',
            'deliveries'    => [
                ['type' => 'email', 'value' => 'john@doe.com'],
            ],
            'args' => [
                'dateFrom' => '2022-01-01T23:00:00.000000Z',
                'dateTo' => '2022-01-01T23:00:00.000000Z',
            ],
        ],
    ];

    $service = new CreateExportService();
    $validator = new NullExportValidator();

    (new CreateExport($service, $validator))->__invoke(null, $args);

    $export = Export::latest('created_at')->first()->refresh();
    expect($export->args)
        ->dateFrom->toBe('2022-01-01')
        ->dateTo->toBe('2022-01-01');
});
