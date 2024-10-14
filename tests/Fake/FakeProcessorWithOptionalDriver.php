<?php

namespace Worksome\DataExport\Tests\Fake;

use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Processor\EloquentProcessor;
use Worksome\DataExport\Processor\ProcessorData;
use Worksome\DataExport\Tests\Fake\Models\User;

class FakeProcessorWithOptionalDriver extends EloquentProcessor
{
    public string $type = 'fake';

    public array $columns = [
        'id' => 'User ID',
        'name',
        'is_admin' => 'Is Admin',
    ];

    public function process(Export $export): ProcessorData
    {
        $query = User::query();

        $data = $this->filterQuery($query);

        return new ProcessorData($data, $this->type);
    }

    public function optional($item): array
    {
        return [
            ['Compliance UK' => 'applies'],
            ['Compliance US' => 'none'],
        ];
    }
}
