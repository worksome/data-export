<?php

namespace Worksome\DataExport\Generator;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Worksome\DataExport\Generator\Contracts\GeneratorDriver;
use Worksome\DataExport\Processor\ProcessorData;

class CsvDriver implements GeneratorDriver
{
    private const DELIMITER = ',';

    private const ENCLOSURE = '"';

    private const LINE_ENDING = "\r\n";

    /** Rows are written to memory up to this size, and to a temp file beyond it. */
    private const BUFFER_BYTES = 8 * 1024 * 1024;

    public function generate(ProcessorData $processorData): GeneratorFile
    {
        $filename = sprintf(
            'export-%s-%s-%s',
            $processorData->getType(),
            Carbon::now()->format('Y-m-d'),
            Str::random(40)
        );

        return $this->saveToStorage($filename, $this->toStream($processorData), $processorData);
    }

    /**
     * The whole file as a string. Streams via generate() instead for anything
     * large enough that holding it in memory matters.
     */
    public function exportToCsv(ProcessorData $processorData): string
    {
        $stream = $this->toStream($processorData);

        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        return $contents === false ? '' : $contents;
    }

    /**
     * @param resource $stream
     */
    public function saveToStorage($filenameWithoutExtension, $stream, ProcessorData $processorData): GeneratorFile
    {
        $filepath = sprintf('exports/%s.csv', $filenameWithoutExtension);

        rewind($stream);
        Storage::writeStream($filepath, $stream);
        fclose($stream);

        return new GeneratorFile(
            path: $filepath,
            size: Storage::size($filepath),
            url: Storage::url($filepath),
            count: count($processorData->getData()),
            mimeType: 'text/csv',
        );
    }

    /**
     * @return resource
     */
    public function toStream(ProcessorData $processorData)
    {
        $stream = fopen('php://temp/maxmemory:' . self::BUFFER_BYTES, 'w+b');

        if ($stream === false) {
            throw new \RuntimeException('Unable to open a stream to write the export to.');
        }

        $this->writeCsv($processorData, $stream);

        return $stream;
    }

    /**
     * Writes the header and every row to $stream, and returns the rows written.
     *
     * @param resource $stream
     */
    /**
     * Writes the header and every row to $stream, and returns the rows written.
     *
     * @param resource $stream
     */
    public function writeCsv(ProcessorData $processorData, $stream): int
    {
        $rows = $processorData->getData();
        $columns = $this->columns($rows);

        if ($columns === []) {
            return 0;
        }

        $this->writeRow($stream, $columns);

        $written = 0;

        foreach ($rows as $row) {
            $fields = [];

            foreach ($columns as $column) {
                $fields[] = $row[$column] ?? '';
            }

            $this->writeRow($stream, $fields);
            $written++;
        }

        return $written;
    }

    /**
     * The header names every column any row has, in the order first seen, so a
     * row whose keys differ cannot line up against the wrong header.
     *
     * @param array<array-key, array<array-key, mixed>> $rows
     *
     * @return array<int, array-key>
     */
    private function columns(array $rows): array
    {
        $columns = [];

        foreach ($rows as $row) {
            foreach (array_keys($row) as $key) {
                $columns[$key] = true;
            }
        }

        return array_keys($columns);
    }

    /**
     * @param resource                $stream
     * @param array<array-key, mixed> $fields
     */
    private function writeRow($stream, array $fields): void
    {
        $line = implode(self::DELIMITER, array_map(
            fn (mixed $field): string => $this->field((string) $field),
            $fields
        ));

        fwrite($stream, $line . self::LINE_ENDING);
    }

    /**
     * A value is quoted only when leaving it bare would break the row, so the
     * file keeps the shape it has always had. A bare carriage return counts,
     * because rows are separated by one.
     */
    private function field(string $value): string
    {
        if (strpbrk($value, self::DELIMITER . self::ENCLOSURE . "\r\n") === false) {
            return $value;
        }

        return self::ENCLOSURE
            . str_replace(self::ENCLOSURE, self::ENCLOSURE . self::ENCLOSURE, $value)
            . self::ENCLOSURE;
    }
}
