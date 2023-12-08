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

    /**
     * Additional optional data. Useful to normalize
     * data for multiple items where each item might
     * have slightly different set. E.g. optional
     */
    public function optional($item): array
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
            // Combine columns plus additional and optional fields
            $filtered = $data->map(function ($item) use ($columns, $allowedColumns) {
                $additional = $this->additional($item);
                $optional = $this->optional($item);
                $additionalKeys = array_keys($additional);

                // Merge fieldsets
                $item = collect(array_merge($item->toArray(), $additional));

                // Only let through the desired columns
                $item = $item->only($allowedColumns->concat($additionalKeys));

                // Ensure some values are properly casted
                $item = $item->map(function ($value) {
                    if (is_bool($value)) {
                        $value = (int) $value;
                    }

                    return strval($value);
                });

                // add optional into the item as array so we can later
                // normalize the columns
                $item->put('optional', $optional);

                // update keys based on desired key names provided in the processor
                $item = $item->keyBy(function ($value, $key) use ($columns) {
                    return $columns->get($key, $key);
                })->all();

                // we return an array
                return $item;
            });

            $items->push($filtered);
        });

        $formattedItems = $this->normalizeOptionalItems($items->flatten(1)->toArray());

        return $formattedItems;
    }

    protected function getAllOptionalKeys(array $items): array
    {
        $optionalKeys = [];

        foreach ($items as $item) {
            $optional = $item['optional'];

            if (count($optional)) {
                foreach ($item['optional'] as $optional) {
                    $key = key($optional);
                    if (! in_array($key, $optionalKeys)) {
                        $optionalKeys[] = $key;
                    }
                }
            }
        }

        return $optionalKeys;
    }

    protected function normalizeOptionalItems(array $items): array
    {
        // get optional keys
        $optionalKeys = $this->getAllOptionalKeys($items);

        // go through each item
        foreach ($items as $key => $item) {
            $itemOptionals = call_user_func_array('array_merge', $items[$key]['optional']);

            // for each item we wanna add each optional key
            // and also populate value if we got one
            foreach ($optionalKeys as $optionalArrKey => $optionalKey) {
                $value = '';

                // check if we have value for this item and populate it
                if (isset($itemOptionals[$optionalKey])) {
                    $value = $itemOptionals[$optionalKey];
                }

                // add the optional key and it's value
                $items[$key][$optionalKey] = $value;
            }

            // unset our "temp" optional array as now we have them
            // in the correct format
            unset($items[$key]['optional']);
        }

        return $items;
    }
}
