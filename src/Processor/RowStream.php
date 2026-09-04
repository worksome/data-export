<?php

declare(strict_types=1);

namespace Worksome\DataExport\Processor;

use Countable;
use Generator;
use IteratorAggregate;
use RuntimeException;

/**
 * The rows of an export, kept in a buffer that spills to disk rather than in memory.
 *
 * A row is written the moment it is built, before the full set of columns is
 * known - an optional column only exists once some row has a value for it -
 * so reading the rows back fills each one out to the final column list.
 *
 * @implements IteratorAggregate<int, array<array-key, mixed>>
 */
final class RowStream implements IteratorAggregate, Countable
{
    /** Rows stay in memory up to this size and go to a temp file beyond it. */
    private const BUFFER_BYTES = 8 * 1024 * 1024;

    /** @var resource */
    private $buffer;

    /** @var array<array-key, true> the fixed columns, in the order first seen */
    private array $columns = [];

    /** @var array<array-key, true> the optional columns, in the order first seen */
    private array $optionalColumns = [];

    private int $count = 0;

    public function __construct()
    {
        $buffer = fopen('php://temp/maxmemory:' . self::BUFFER_BYTES, 'w+b');

        if ($buffer === false) {
            throw new RuntimeException('Unable to open a buffer to hold the export rows.');
        }

        $this->buffer = $buffer;
    }

    public function __destruct()
    {
        if (is_resource($this->buffer)) {
            fclose($this->buffer);
        }
    }

    /**
     * @param array<array-key, mixed>             $row      the row's fixed columns
     * @param array<int, array<array-key, mixed>> $optional one single-entry array per optional value
     */
    public function push(array $row, array $optional): void
    {
        foreach (array_keys($row) as $key) {
            $this->columns[$key] = true;
        }

        foreach ($optional as $entry) {
            foreach (array_keys($entry) as $key) {
                $this->optionalColumns[$key] = true;
            }
        }

        fwrite($this->buffer, json_encode(
            [$row, $optional],
            JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES
        ) . "\n");

        $this->count++;
    }

    /**
     * The fixed columns first, then any optional column that is not already one of them.
     *
     * @return array<int, array-key>
     */
    public function columns(): array
    {
        return array_merge(
            array_keys($this->columns),
            array_keys(array_diff_key($this->optionalColumns, $this->columns)),
        );
    }

    public function count(): int
    {
        return $this->count;
    }

    /**
     * Every row, filled out to the final column list. Can be iterated more than once.
     *
     * @return Generator<int, array<array-key, mixed>>
     */
    public function getIterator(): Generator
    {
        $columns = $this->columns();

        rewind($this->buffer);

        while (($line = fgets($this->buffer)) !== false) {
            /** @var array{0: array<array-key, mixed>, 1: array<int, array<array-key, mixed>>} $decoded */
            $decoded = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            [$row, $optional] = $decoded;

            $optionals = $optional === [] ? [] : array_merge(...$optional);

            $filled = [];

            foreach ($columns as $column) {
                // An optional column wins over a fixed one of the same name, and a
                // row without a value for it gets an empty string.
                if (isset($this->optionalColumns[$column])) {
                    $filled[$column] = isset($optionals[$column]) ? $optionals[$column] : '';
                } else {
                    $filled[$column] = $row[$column] ?? '';
                }
            }

            yield $filled;
        }
    }
}
