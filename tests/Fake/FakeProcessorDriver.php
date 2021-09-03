<?php

namespace Worksome\DataExport\Tests\Fake;

use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Processor\EloquentProcessor;
use Worksome\DataExport\Processor\ProcessorData;
use Worksome\DataExport\Tests\Fake\Models\User;

class FakeProcessorDriver extends EloquentProcessor
{
    public string $type = 'fake';

    public array $columns = [
        'id',
        'name',
    ];

    public function process(Export $export): ProcessorData
    {
        $query = User::query();

        $data = $this->filterQuery($query);

        return new ProcessorData($data);
    }
}
