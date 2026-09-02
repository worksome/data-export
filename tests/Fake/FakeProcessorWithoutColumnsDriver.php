<?php

namespace Worksome\DataExport\Tests\Fake;

use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Processor\EloquentProcessor;
use Worksome\DataExport\Processor\ProcessorData;
use Worksome\DataExport\Tests\Fake\Models\Team;

class FakeProcessorWithoutColumnsDriver extends EloquentProcessor
{
    public string $type = 'fake';

    public array $columns = [];

    public function process(Export $export): ProcessorData
    {
        // Members are deliberately not eager-loaded, so serialising a team's
        // attributes runs its appended accessor and queries them.
        $query = Team::query();

        $data = $this->filterQuery($query);

        return new ProcessorData($data, $this->type);
    }

    /** @param Team $item */
    public function additional($item): array
    {
        return ['Team' => $item->name];
    }
}
