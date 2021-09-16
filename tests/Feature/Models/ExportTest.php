<?php

namespace Worksome\DataExport\Tests\Feature\Models;

use Worksome\DataExport\Models\Export;

it('can get the export size in a human-readable format', function () {
    $export = new Export([
        'size' => '9089',
    ]);

    expect($export->getFormattedSize())->toBe('8.88kb');
});
