<?php

namespace Worksome\DataExport\Processor;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Worksome\DataExport\Processor\Contracts\ProcessorDriver;

abstract class EloquentProcessor implements ProcessorDriver
{
    /**
     * The export type.
     *
     * @var string
     */
    protected string $type = '';

    /**
     * Actual column names that are present inside the given database table.
     *
     * @var array
     */
    protected array $columns = [];

    /**
     * Additional data fields to be included for each record in the export.
     *
     * @return array
     */
    public function additional($item): array
    {
        return [];
    }

    public function formatDate(string $date): string
    {
        return Carbon::parse($date)->format('Y-m-d');
    }

    protected function columns(): Collection
    {
        return collect($this->columns)->keyBy(function ($value, $key) {
            if (is_numeric($key)) {
                return $value;
            }

            return $key;
       });
    }

    protected function filterQuery(Builder $query): array
    {
        $columns = $this->columns();
        $allowedColumns = $columns->keys();

        $items = collect();

        $query->chunk(1000, function ($data) use ($columns, $allowedColumns, $items) {
            // Combine columns plus additional fields
            $filtered = $data->map(function ($item) use ($columns, $allowedColumns) {
                $additional = $this->additional($item);
                $additionalKeys = array_keys($additional);

                // Merge fieldsets
                $item = collect(array_merge($item->toArray(), $additional));

                // Only let through the desired columns
                $item = $item->only($allowedColumns->concat($additionalKeys));

                return $item->keyBy(function ($value, $key) use ($columns) {
                    return $columns->get($key, $key);
                })->all();
            });

            $items->push($filtered);
        });

        return $items->flatten(1)->toArray();
    }
}
