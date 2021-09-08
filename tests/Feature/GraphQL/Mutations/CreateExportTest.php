<?php

namespace Worksome\DataExport\Tests\Feature\GraphQL;

use Illuminate\Support\Facades\Event;
use Worksome\DataExport\Enums\ExportResponseStatus;
use Worksome\DataExport\Events\ExportInitialised;
use Worksome\DataExport\GraphQL\Mutations\CreateExport;
use Worksome\DataExport\GraphQL\NullExportValidator;
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

    expect($response['status'])->toBe(ExportResponseStatus::SUCCESS);
});
