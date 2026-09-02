<?php

namespace Worksome\DataExport\Tests\Fake;

use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Processor\EloquentProcessor;
use Worksome\DataExport\Processor\ProcessorData;
use Worksome\DataExport\Tests\Fake\Models\Member;

class FakeProcessorWithCustomToArrayDriver extends EloquentProcessor
{
    public string $type = 'fake';

    public array $columns = [
        'id' => 'User ID',
        'email' => 'Email',
    ];

    public function process(Export $export): ProcessorData
    {
        $query = Member::query();

        $data = $this->filterQuery($query);

        return new ProcessorData($data, $this->type);
    }
}
