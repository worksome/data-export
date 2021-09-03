<?php

namespace Worksome\DataExport\Tests\Feature\Services;

use Worksome\DataExport\Services\CreateExportDTO;

it('can initialize the dto from the constructor', function () {
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

    expect($dto->getUserId())->toBe($args['userId']);
    expect($dto->getAccountId())->toBe($args['accountId']);
    expect($dto->getAccountType())->toBe($args['accountType']);
    expect($dto->getType())->toBe($args['type']);
    expect($dto->getGeneratorType())->toBe($args['generatorType']);
    expect($dto->getDeliveries())->toBe($args['deliveries']);
    expect($dto->getArgs())->toBe($args['args']);
});

it('can initialize the dto from the from args method', function () {
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

    $dto = CreateExportDTO::fromArgs($args);

    expect($dto->getUserId())->toBe($args['userId']);
    expect($dto->getAccountId())->toBe($args['accountId']);
    expect($dto->getAccountType())->toBe($args['accountType']);
    expect($dto->getType())->toBe($args['type']);
    expect($dto->getGeneratorType())->toBe($args['generatorType']);
    expect($dto->getDeliveries())->toBe($args['deliveries']);
    expect($dto->getArgs())->toBe($args['args']);
});
