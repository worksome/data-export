<?php

namespace Worksome\DataExport\Tests\Feature\Notifications;

use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Notifications\SuccessfulExportNotification;

it('can create a successful notification', function () {
    $export = new Export();

    $notification = new SuccessfulExportNotification($export);

    expect($notification->export)->not->toBeNull();
    expect($export)->toBe($notification->export);
});
