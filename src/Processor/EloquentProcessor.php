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
     * Additional data connected to compliances.
     */
    public function compliances($item): array
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
            // Combine columns plus additional fields and compliances
            $filtered = $data->map(function ($item) use ($columns, $allowedColumns) {
                $additional = $this->additional($item);
                $compliances = $this->compliances($item);
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

                // add compliances into the item as array so we can later
                // normalize the columns
                $item->put('compliances', $compliances);

                // update keys based on desired key names provided in the processor
                $item = $item->keyBy(function ($value, $key) use ($columns) {
                    return $columns->get($key, $key);
                })->all();

                // we return an array
                return $item;
            });

            $items->push($filtered);
        });

        $formattedItems = $this->normalizeComplianceItems($items->flatten(1)->toArray());

        return $formattedItems;
    }

    protected function getAllComplianceKeys(array $items): array
    {
        $complianceKeys = [];

        foreach ($items as $item) {
            $compliances = $item['compliances'];

            if (count($compliances)) {
                foreach ($item['compliances'] as $compliance) {
                    $key = key($compliance);
                    if (!in_array($key, $complianceKeys)) {
                        $complianceKeys[] = $key;
                    }
                }
            }
        }

        return $complianceKeys;
    }

    protected function normalizeComplianceItems(array $items): array
    {
        // get compliance keys
        $complianceKeys = $this->getAllComplianceKeys($items);

        // go through each item
        foreach ($items as $key => $item) {
            $itemCompliances = $items[$key]['compliances'];

            // for each item we wanna add each compliance key
            // and also populate value if we got one
            foreach ($complianceKeys as $complianceArrKey => $complianceKey) {
                $value = '';

                // check if we have value for this item and populate it
                if (isset($itemCompliances[$complianceArrKey][$complianceKey])) {
                    $value = $itemCompliances[$complianceArrKey][$complianceKey];
                }

                // add the compliance key and it's value
                $items[$key][$complianceKey] = $value;
            }

            // unset our "temp" compliances array as now we have them
            // in the correct format
            unset($items[$key]['compliances']);
        }

        return $items;
    }
}
