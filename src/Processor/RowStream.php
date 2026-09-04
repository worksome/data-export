<?php

declare(strict_types=1);

namespace Worksome\DataExport\Processor;

use Countable;
use Generator;
use IteratorAggregate;
use RuntimeException;
use Stringable;

/**
 * The rows of an export, kept in a buffer that spills to disk rather than in memory.
 *
 * A row is written the moment it is built, before the full set of columns is
 * known - an optional column only exists once some row has a value for it -
 * so reading the rows back fills each one out to the final column list.
 *
 * @implements IteratorAggregate<int, array<array-key, string>>
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
     * Values are stored as the strings the file will contain, so nothing is lost
     * or reinterpreted on the way through the buffer.
     *
     * @param array<array-key, mixed>             $row      the row's fixed columns
     * @param array<int, array<array-key, mixed>> $optional one single-entry array per optional value
     */
    public function push(array $row, array $optional): void
    {
        $fixed = [];

        foreach ($row as $key => $value) {
            $this->columns[$key] = true;
            $fixed[$key] = $this->stringify($value);
        }

        // A later optional value for the same column wins, as array_merge would have it.
        $merged = [];

        foreach ($optional as $entry) {
            foreach ($entry as $key => $value) {
                $this->optionalColumns[$key] = true;
                $merged[$key] = $this->stringify($value);
            }
        }

        // Length-framed so a value may contain anything, including newlines.
        $payload = serialize([$fixed, $merged]);
        $frame = strlen($payload) . "\n" . $payload;

        if (fwrite($this->buffer, $frame) !== strlen($frame)) {
            throw new RuntimeException('Unable to write an export row to the buffer.');
        }

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
     * @return Generator<int, array<array-key, string>>
     */
    public function getIterator(): Generator
    {
        $columns = $this->columns();

        rewind($this->buffer);

        while (($header = fgets($this->buffer)) !== false) {
            $payload = $this->read((int) $header);

            /** @var array{0: array<array-key, string>, 1: array<array-key, string>} $decoded */
            $decoded = unserialize($payload, ['allowed_classes' => false]);
            [$fixed, $optionals] = $decoded;

            $filled = [];

            foreach ($columns as $column) {
                // An optional column wins over a fixed one of the same name, and a
                // row without a value for it gets an empty string.
                $filled[$column] = isset($this->optionalColumns[$column])
                    ? ($optionals[$column] ?? '')
                    : ($fixed[$column] ?? '');
            }

            yield $filled;
        }
    }

    private function read(int $length): string
    {
        $payload = '';

        while (strlen($payload) < $length) {
            $chunk = fread($this->buffer, $length - strlen($payload));

            if ($chunk === false || $chunk === '') {
                throw new RuntimeException('The export row buffer is truncated.');
            }

            $payload .= $chunk;
        }

        return $payload;
    }

    private function stringify(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value) || $value instanceof Stringable) {
            return (string) $value;
        }

        throw new RuntimeException(
            sprintf('An export value must be a string or stringable, %s given.', get_debug_type($value))
        );
    }
}
