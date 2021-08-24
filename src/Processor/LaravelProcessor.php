<?php

namespace Worksome\DataExport\Processor;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Processor\Contracts\Processor as ProcessorContract;

abstract class LaravelProcessor implements ProcessorContract
{
    /**
     * Export type
     * @var mixed
     */
    protected $type = null;

    /**
     * Actual column names that are present inside the given database table
     * @var array
     */
    protected $columns = [];

    /**
     * Additional data fields to be included for each record in the export
     * @return array
     */
    public function additional($item): array
    {
        return [];
    }

    public function fromDate(Export $export): string
    {
        return isset($export->args['dateFrom'])
            ? Carbon::parse($export->args['dateFrom'])->format('Y-m-d')
            : '1970-01-01';

    }

    public function toDate(Export $export): string
    {
        return isset($export->args['dateTo'])
            ? Carbon::parse($export->args['dateTo'])->format('Y-m-d')
            : now()->format('Y-m-d');
    }

    protected function columns(): Collection
    {
        $columns = collect($this->columns)->keyBy(function ($value, $key) {
            if (is_numeric($key)) {
                return $value;
            } else {
                return $key;
            }
       });

        return $columns;
    }

    protected function filterQuery($query): array
    {
        $items = [];
        $query->chunk(1000, function ($data) use (&$items) {
            $columns = $this->columns();
            $allowedColumns = $columns->keys();

            // combine columns plus additional fields
            $filtered = $data->map(function ($item) use (&$allowedColumns) {
                $additional = $this->additional($item);
                $additionalKeys = array_keys($additional);

                // merge fieldsets
                $item = collect(array_merge($item->toArray(), $this->additional($item)));
                // only let through the desired ones
                $item = $item->only($allowedColumns->concat($additionalKeys)->all());

                return $item->toArray();
            });

            // update key (aka label for the column) if
            // takes value for key of the $columns array and replaces the key with the value
            // if present
            $filtered = $filtered->map(function ($item) use ($columns) {
                return collect($item)->keyBy(function ($value, $key) use ($columns) {
                    if ($columns->get($key)) {
                        return $columns->get($key);
                    }

                    return $key;
                })->all();
            });

            $items[] = $filtered->toArray();
        });

        return collect($items)->flatten(1)->toArray();
    }
}
