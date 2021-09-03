<?php

namespace Worksome\DataExport\Tests\Feature\Processor;

use Worksome\DataExport\Processor\ProcessorData;

it('can retrieve the data', function () {
    $processorData = new ProcessorData([
        'foo' => 'Bar'
    ]);

    expect($processorData->getData())->toBe([
        'foo' => 'Bar',
    ]);
});
