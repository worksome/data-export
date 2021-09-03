<?php

namespace Worksome\DataExport\Tests\Feature\Models;

use Worksome\DataExport\Models\Export;

it('can retrieve all the delivery channels', function () {
    $export = new Export([
        'deliveries' => [
            ['type' => 'email', 'value' => 'john@doe.com'],
            ['type' => 'inapp'],
        ],
    ]);

    expect($export->getDeliveryChannels())->toBe([
        'email',
        'inapp',
    ]);
});

it('can get the delivery information for a certain type', function () {
    $export = new Export([
        'deliveries' => [
            ['type' => 'email', 'value' => 'john@doe.com'],
        ],
    ]);

    expect($export->getDeliveryFor('email'))->toBe([
        'type' => 'email',
        'value' => 'john@doe.com',
    ]);

    expect($export->getDeliveryFor('inapp'))->toBeNull();
});

it('can get the export size in a human-readable format', function () {
    $export = new Export([
        'size' => '9089',
    ]);

    expect($export->getFormattedSize())->toBe('8.88kb');
});
