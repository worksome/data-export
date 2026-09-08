<?php

namespace Worksome\DataExport\Processor;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ReflectionMethod;
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
     * Whether a model class declares its own toArray(), keyed by class name.
     *
     * @var array<class-string, bool>
     */
    private array $hasCustomToArray = [];

    /**
     * Additional data fields to be included for each record in the export.
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

    /**
     * Relations can never be exported columns, so only the attributes are needed.
     * A model with its own toArray() may reshape those, so it keeps the full call.
     *
     * @return array<string, mixed>
     */
    private function serialiseAttributes(Model $item): array
    {
        $class = $item::class;

        if (! isset($this->hasCustomToArray[$class])) {
            $this->hasCustomToArray[$class] = (new ReflectionMethod($class, 'toArray'))
                ->getDeclaringClass()
                ->getName() !== Model::class;
        }

        return $this->hasCustomToArray[$class]
            ? $item->toArray()
            : $item->attributesToArray();
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

    protected function filterQuery(Builder $query): RowStream
    {
        $columns = $this->columns();
        $allowedColumns = $columns->keys();

        $rows = new RowStream();

        $query->chunk(1000, function ($data) use ($columns, $allowedColumns, $rows) {
            foreach ($data as $item) {
                $additional = $this->additional($item);
                $optional = $this->optional($item);
                $additionalKeys = array_keys($additional);

                // Nothing here survives when no columns are declared, and relations
                // can never be columns, so both were pure cost before.
                $attributes = $allowedColumns->isEmpty() ? [] : $this->serialiseAttributes($item);

                // Merge fieldsets
                $row = collect(array_merge($attributes, $additional));

                // Only let through the desired columns
                $row = $row->only($allowedColumns->concat($additionalKeys));

                // Ensure some values are properly casted
                $row = $row->map(function ($value) {
                    if (is_bool($value)) {
                        $value = (int) $value;
                    }

                    return strval($value);
                });

                // update keys based on desired key names provided in the processor
                $row = $row->keyBy(function ($value, $key) use ($columns) {
                    return $columns->get($key, $key);
                })->all();

                $rows->push($row, $optional);
            }
        });

        return $rows;
    }
}
