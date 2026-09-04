<?php

namespace Worksome\DataExport\Processor;

class ProcessorData
{
    /**
     * Rows either already built as an array, or held in a stream that can be read
     * more than once. A one-shot iterable would be exhausted by the first reader.
     *
     * @param array<array-key, array<array-key, mixed>>|RowStream $data
     */
    public function __construct(
        private readonly array|RowStream $data,
        private readonly string $type,
    ) {
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
        return count($this->data);
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
