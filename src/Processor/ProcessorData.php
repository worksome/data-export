<?php

namespace Worksome\DataExport\Processor;

class ProcessorData
{
    /**
     * @param iterable<array-key, array<array-key, mixed>> $data
     */
    public function __construct(
        private readonly iterable $data,
        private readonly string $type,
    ) {
    }

    /**
     * Every row, loaded into memory. Prefer rows() for anything large.
     *
     * @return array<array-key, array<array-key, mixed>>
     */
    public function getData(): array
    {
        return is_array($this->data) ? $this->data : iterator_to_array($this->data, false);
    }

    /**
     * The rows, one at a time.
     *
     * @return iterable<array-key, array<array-key, mixed>>
     */
    public function rows(): iterable
    {
        return $this->data;
    }

    public function getCount(): int
    {
        return is_countable($this->data) ? count($this->data) : count($this->getData());
    }

    /**
     * Every column any row has, in the order first seen.
     *
     * @return array<int, array-key>
     */
    public function getColumns(): array
    {
        if ($this->data instanceof RowStream) {
            return $this->data->columns();
        }

        $columns = [];

        foreach ($this->data as $row) {
            foreach (array_keys($row) as $key) {
                $columns[$key] = true;
            }
        }

        return array_keys($columns);
    }

    public function getType(): string
    {
        return $this->type;
    }
}
