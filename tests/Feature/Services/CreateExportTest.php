<?php

namespace Worksome\DataExport\Tests\Feature\Services;

use Worksome\DataExport\Enums\ExportStatus;
use Worksome\DataExport\Services\CreateExport;
use Worksome\DataExport\Services\CreateExportDTO;

it('can create an export', function () {
    $args = [
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
    ];

    $dto = new CreateExportDTO(...$args);

    $service = new CreateExport();
    $service->fromDTO($dto);

    $result = $service->run();

    expect($result->status)->toBe(ExportStatus::AWAITING);
    expect($result->user_id)->toBe($args['userId']);
    expect($result->account_id)->toBe($args['accountId']);
    expect($result->account_type)->toBe($args['accountType']);
    expect($result->type)->toBe($args['type']);
    expect($result->generator_type)->toBe($args['generatorType']);
    expect($result->deliveries->toArray())->toBe($args['deliveries']);
    expect($result->args)->toBe($args['args']);
});
